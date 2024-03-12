<?php

namespace App\Services;

use DateTime;
use App\Models\Card;
use App\Models\Invoice;
use App\Models\Category;
use App\Models\InvoiceEntry;
use App\Services\CardService;
use App\Exceptions\InsufficientLimitException;
use App\Repositories\Interfaces\ParcelRepositoryInterface;
use App\Repositories\Interfaces\InvoiceRepositoryInterface;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Repositories\Interfaces\InvoiceEntryRepositoryInterface;

class InvoiceEntryService
{
    protected $repository;
    protected $invoiceRepository;
    protected $parcelRepository;
    protected $tagService;

    public function __construct(
        InvoiceEntryRepositoryInterface $repository,
        InvoiceRepositoryInterface $invoiceRepository,
        ParcelRepositoryInterface $parcelRepository,
        TagService $tagService
    ) {
        $this->repository           = $repository;
        $this->invoiceRepository    = $invoiceRepository;
        $this->parcelRepository     = $parcelRepository;
        $this->tagService           = $tagService;
    }

    public function create(Card $card, array $data)
    {
        $categoryRepository = app(CategoryRepositoryInterface::class);
        $category = $categoryRepository->findById($data['category_id']);

        if ($data['value'] > $card->balance && $category->isExpense()) {
            throw new InsufficientLimitException(__('messages.entries.insufficient_limit'));
        }

        $data['installment'] = isset($data['installment']) && $data['installment'] ? true : false;

        if ($data['installment']) {
            $entry =  $this->createInvoiceEntryParcels($card, $data);
        } else {
            if (app()->runningInConsole()) {
                unset($data['installment']);
            }
            
            $entry = $this->createEntry($card, $data);
        }

        $cardService = app(CardService::class);
        $cardService->updateCardBalance($card);

        return $entry;
    }

    /**
     * Create the invoice entry
     */
    public function createEntry(Card $card, $data)
    {
        $invoice = $this->invoiceRepository->getInvoiceByDate($card, $data['date']);

        if (!$invoice) {
            return false;
        }

        $data['invoice_id'] = $invoice->id;

        $entry = $this->repository->create($data);

        if (isset($data["tags"]) && $data["tags"]) {
            $this->tagService->createInvoiceEntryTags($entry, $data["tags"]);
        }

        if (!$entry->hasParcels()) {
            $this->invoiceRepository->updateInvoiceAmount($invoice);
        }

        return $entry;
    }

    /**
     * Creates all the invoice entry parcels
     */
    public function createInvoiceEntryParcels(Card $card, array $data)
    {
        $data['has_parcels'] = true;

        $entry = $this->createEntry($card, $data);

        $parcels        = [];
        $total_parcels  = $data['installments_number'];

        $parcel_data = [
            'total_parcels' => $total_parcels,
            'parcel_value'  => $entry->value / $data['installments_number'],
            'category_id'   => $entry->category->id,
        ];

        $date = new DateTime($entry->date);

        for ($parceling = 1; $parceling <= $total_parcels; $parceling++) {
            $invoice = $this->invoiceRepository->getInvoiceByDate($card, $date->format('Y-m-d'));

            $parcel_data['date']            = $date->format('Y-m-d');
            $parcel_data['description']     = $entry->description . " {$parceling}/{$total_parcels}";
            $parcel_data['parcel_number']   = $parceling;
            $parcel_data['invoice_id']      = $invoice->id;

            $parcel = $this->repository->createInvoiceEntryParcel($entry, $parcel_data);

            if ($entry->tags) {
                $this->tagService->createParcelTags($parcel, $entry->tags);
            }

            $this->invoiceRepository->updateInvoiceAmount($invoice);

            $parcels[] = $parcel;

            $date = $date->modify('+1 month');

        }

        return $entry->parcels;
    }

    public function update(InvoiceEntry $entry, array $data)
    {
        $data['monthly'] = isset($data['monthly']) && $data['monthly'] === 'on' ? true : false;

        if ($data['value'] > $entry->invoice->card->balance) {
            throw new InsufficientLimitException(__('messages.entries.insufficient_limit'));
        }

        if (isset($data["tags"])) {
            $this->tagService->createInvoiceEntryTags($entry, $data["tags"]);
        }

        return $this->repository->update($entry, $data);
    }

    public function delete(InvoiceEntry $entry)
    {
        if ($entry->hasParcels()) {
            $this->deleteParcels($entry);
        }
        $entry->tags()->sync([]);
        return $this->repository->delete($entry);
    }

    public function deleteParcels(InvoiceEntry $entry): void
    {
        foreach ($entry->parcels as $parcel) {
            $invoice = $parcel->invoice;
            $parcel->tags()->sync([]);
            $parcel->delete();

            $this->invoiceRepository->updateInvoiceAmount($invoice);
        }
    }

    public function findById($id)
    {
        return $this->repository->findById($id);
    }

    /**
     * Returns an array with the total grouped by category for a given date
     *
     * @param string $date
     * @param int $category_type
     * @return array
     */
    public function getTotalByCategoryForChart($date, $category_type = Category::EXPENSE): array
    {
        $new_date   = new DateTime($date);
        $filter     = [
            'month' => $new_date->format('m'),
            'year'  => $new_date->format('Y')
        ];

        $result = [];
        
        $total = $this->repository->getTotalByCategoryForChart($filter, $category_type);

        foreach ($total as $value) {
            $result[] = [
                'value' => $value['total'] / 100,
                'label' => $value['name']
            ];
        }

        return $result;
    }

    /**
     * Returns the total values of entries by category type for a given date
     *
     * @param int $categoryType
     * @param array $filter
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getTotalByCategoryTypeForRangeDate($categoryType, array $filter)
    {
        $entries = $this->repository->getTotalByCategoryTypeForRangeDate($categoryType, $filter);

        $parcels = $this->parcelRepository->getTotalByCategoryTypeForRangeDate($categoryType, $filter);

        return $entries->concat($parcels);
    }

    /**
     * Returns the entries for the given category id and range date
     *
     * @param int $categoryType
     * @param array $filter
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getEntriesByCategoryAndRangeDate($from, $to, $category_id, $tags)
    {
        $idTags = $this->tagService->getTagsIds($tags);

        $entries = $this->repository->getEntriesByCategoryAndRangeDate($from, $to, $category_id, $idTags);

        $parcelRepository = app(ParcelRepositoryInterface::class);

        $parcels = $parcelRepository->getParcelsByCategoryAndRangeDate($from, $to, $category_id, $idTags);

        $result = $entries->concat($parcels);

        return $result->sortBy('date');
    }

    /**
     * Returns the total values of entries by category type for a given date
     *
     * @param int $categoryType
     * @param string $date
     * @return float
     */
    public function getTotalMonthlyByCategory($categoryType, $date): float
    {
        $new_date   = new DateTime($date);
        $filter     = [
            'month' => $new_date->format('m'),
            'year' => $new_date->format('Y')
        ];

        return $this->repository->getTotalMonthlyByCategory($categoryType, $filter);
    }

    /**
     * Returns entries for a given invoice
     *
     * @param Invoice $invoice
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getAllEntriesForInvoice(Invoice $invoice)
    {
        $entries = $this->repository->getEntries($invoice);

        $parcelRepository = app(ParcelRepositoryInterface::class);
        
        $parcels = $parcelRepository->getParcelsOfInvoice($invoice);

        $all = $entries->concat($parcels);
        
        return $all->sortBy('date');
    }

    /**
     * Returns entries for a given invoice
     *
     * @param InvoiceEntry $invoice_entry
     * @param int $parcel_number
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getOpenParcels($invoice_entry, int $parcel_number)
    {
        return $this->parcelRepository->getOpenParcels($invoice_entry, $parcel_number);
    }

    /**
     * Check if the parcels ids exists
     *
     * @param InvoiceEntry $invoice_entry
     * @param array $parcels_ids
     * @return bool
     */
    public function parcelsExists(InvoiceEntry $invoice_entry, array $parcels_ids): bool
    {
        $count = 0;
        foreach($invoice_entry->parcels as $parcel) {
            if ($parcel->anticipated) continue;

            if (in_array($parcel->id, $parcels_ids)) {
                $count++;
            }
        }

        return $count === count($parcels_ids);
    }

    /**
     * Anticipates the parcels for the current invoice
     *
     * @param array $parcels_ids
     * @return bool
     */
    public function anticipateParcels(Card $card, array $parcels_ids)
    {
        foreach($parcels_ids as $parcel_id) {
            $parcel = $this->parcelRepository->findById($parcel_id);
            $parcel->anticipated = true;
            $parcel->save();

            $this->invoiceRepository->updateInvoiceAmount($parcel->invoice);

            $data = [
                'date'          => now()->format('Y-m-d'),
                'description'   => $parcel->description . ' (' . __('global.anticipated') . ')',
                'value'         => $parcel->value,
                'category_id'   => $parcel->category_id,
                'anticipated'   => true
            ];

            $entry = $this->createEntry($card, $data);
        }

        $this->invoiceRepository->updateInvoiceAmount($entry->invoice);
    }
}
