<?php

namespace App\Services;

use DateTime;
use App\Models\Card;
use App\Models\Category;
use App\Models\InvoiceEntry;
use App\Services\CardService;
use App\Exceptions\InsufficientLimitException;
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
            $entry =  $this->createInstallments($card, $data);
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

        if (! $entry) {
            return false;
        }

        $this->invoiceRepository->updateInvoiceAmount($invoice);

        return $entry;
    }

    /**
     * Create the installments
     */ 
    public function createInstallments(Card $card, array $data)
    {
        $date                   = new DateTime($data['date']);
        $total                  = $data['value'];
        $installments_number    = $data['installments_number'];
        $installment_value      = number_format($total / $installments_number, 2);
        $description_default    = $data['description'];
        $installments           = [];

        $data['value'] = $installment_value;

        for ($i = 1; $i <= $installments_number; $i++) {
            $data['date']           = $date->format('Y-m-d');
            $data['description']    = $description_default . " {$i}/{$installments_number}" ;           

            $entry = $this->createEntry($card, $data);

            if (! $entry) {
                return false;
            }
            
            $installments[] = $entry;

            $date = $date->modify('+1 month');
        }

        return $installments;
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
        return $this->repository->delete($entry);
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
}
