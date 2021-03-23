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

    public function __construct(InvoiceEntryRepositoryInterface $repository, InvoiceRepositoryInterface $invoiceRepository)
    {
        $this->repository           = $repository;
        $this->invoiceRepository    = $invoiceRepository;
    }

    public function create(Card $card, array $data)
    {      
        $categoryRepository = app(CategoryRepositoryInterface::class);
        $category = $categoryRepository->findById($data['category_id']);

        if ($data['value'] > $card->balance && $category->isExpense()) {
            throw new InsufficientLimitException(__('messages.entries.insufficient_limit'));
        }

        if (isset($data['installment']) && isset($data['installments_number']) && $data['installments_number'] > 1) {
            $entry =  $this->createInvoiceEntryParcels($card, $data);
        } else {
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

        if (! $invoice) {
            return false;
        }

        $data['invoice_id'] = $invoice->id;

        $entry = $this->repository->create($data);

        if (! $entry->hasParcels()) {
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
            'parcel_value'  => number_format($entry->value / $data['installments_number'], 2),
            'category_id'   => $entry->category->id,
        ];

        $date = new DateTime($entry->date);

        for ($parceling = 1; $parceling <= $total_parcels; $parceling++) {
            $invoice = $this->invoiceRepository->getInvoiceByDate($card, $date->format('Y-m-d'));

            $parcel_data['date']            = $date->format('Y-m-d');
            $parcel_data['description']     = $entry->description . " {$parceling}/{$total_parcels}" ;           
            $parcel_data['parcel_number']   = $parceling;
            $parcel_data['invoice_id']      = $invoice->id;

            $parcel = $this->repository->createInvoiceEntryParcel($entry, $parcel_data);

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

        return $this->repository->update($entry, $data);
    }

    public function delete(InvoiceEntry $entry)
    {
        if ($entry->hasParcels()) {
            $this->deleteParcels($entry);
        }
        return $this->repository->delete($entry);
    }

    public function deleteParcels(InvoiceEntry $entry): void
    {
        foreach ($entry->parcels as $parcel) {
            $invoice = $parcel->invoice;

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
     * @return array
     */ 
    public function getTotalByCategoryTypeForRangeDate($categoryType, array $filter): array
    {       
        return $this->repository->getTotalByCategoryTypeForRangeDate($categoryType, $filter);
    }

    /**
     * Returns the entries for the given category id and range date
     *
     * @param int $categoryType
     * @param array $filter
     * @return array
     */ 
    public function getEntriesByCategoryAndRangeDate($from, $to, $category_id)
    {       
        return $this->repository->getEntriesByCategoryAndRangeDate($from, $to, $category_id);
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
        
        return $entries->concat($parcels);
    }
}
