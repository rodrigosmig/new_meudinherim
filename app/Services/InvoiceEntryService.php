<?php

namespace App\Services;

use DateTime;
use App\Models\Card;
use App\Models\Category;
use App\Models\InvoiceEntry;
use App\Services\CardService;
use App\Exceptions\InsufficientLimitException;

class InvoiceEntryService
{
    protected $entry;
    protected $card;
    protected $data;

    public function __construct(InvoiceEntry $entry)
    {
        $this->entry = $entry;
    }

    public function make(Card $card, array $data)
    {   //dd($data);
        $this->card = $card;
        $this->data = $data;
        
        $categoryService = app(CategoryService::class);
        $category = $categoryService->findById($data['category_id']);

        if ($this->data['value'] > $this->card->balance && $category->isExpense()) {
            throw new InsufficientLimitException(__('messages.entries.insufficient_limit'));
        }

        if (isset($this->data['installment']) && isset($this->data['installments_number']) && $this->data['installments_number'] > 1) {
            $entry =  $this->createInstallments();
        } else {
            $entry = $this->createEntry();
        }

        $entryservice = app(CardService::class);
        $entryservice->updateCardBalance($card);

        return $entry;
    }

    /**
     * Create the invoice entry
     */ 
    public function createEntry()
    {
        $cardService = app(CardService::class);

        $invoice = $cardService->getInvoiceByDate($this->card, $this->data['date']);

        if (! $invoice) {
            return false;
        }

        $entry = $invoice->entries()->create($this->data);

        if (! $entry) {
            return false;
        }

        $invoiceService = app(InvoiceService::class);
        $invoiceService->updateInvoiceAmount($invoice);

        return $entry;
    }

    /**
     * Create the installments
     */ 
    public function createInstallments()
    {
        $date                   = new DateTime($this->data['date']);
        $total                  = $this->data['value'];
        $installments_number    = $this->data['installments_number'];
        $installment_value      = number_format($total / $installments_number, 2);
        $description_default    = $this->data['description'];
        $installments           = [];

        $this->data['value'] = $installment_value;

        for ($i = 1; $i <= $installments_number; $i++) {
            $this->data['date']           = $date->format('Y-m-d');
            $this->data['description']    = $description_default . " {$i}/{$installments_number}" ;           

            $entry = $this->createEntry();

            if (! $entry) {
                return false;
            }
            
            $installments[] = $entry;

            $date = $date->modify('+1 month');
        }

        return $installments;
    }

    public function update($id, array $data)
    {
        $data['monthly'] = isset($data['monthly']) && $data['monthly'] === 'on' ? true : false;

        $entry = $this->findById($id);

        if(! $entry) {
            return false;
        }

        if ($data['value'] > $entry->invoice->card->balance) {
            throw new InsufficientLimitException(__('messages.entries.insufficient_limit'));
        }

        $result = $entry->update($data);

        if (! $result) {
            return false;
        }

        return $entry;
    }

    public function delete($id)
    {
        $entry = $this->findById($id);

        if(! $entry) {
            return false;
        }

        return $entry->delete();
    }

    public function findById($id)
    {
        return $this->entry->find($id);
    }

    /**
     * Returns an array with the total grouped by category for a given date
     *
     * @param string $date
     * @return array
     */ 
    public function getTotalByCategoryForChart($date): array
    {
        $new_date   = new DateTime($date);
        $month      = $new_date->format('m');
        $year       = $new_date->format('Y');
        $result     = [];
        
        $total = $this->entry::selectRaw('categories.name, SUM(invoice_entries.value) as total')
            ->join('categories', 'categories.id', '=', 'invoice_entries.category_id')
            ->where('categories.type', Category::EXPENSE)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->groupBy('categories.name')
            ->get()
            ->toArray();

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
        return $this->entry::selectRaw('categories.name as category, categories.id, SUM(invoice_entries.value) / 100 as total, count(*) as quantity')
            ->join('categories', 'categories.id', '=', 'invoice_entries.category_id')
            ->where('categories.type', $categoryType)
            ->where('date', '>=', $filter['from'])
            ->where('date', '<=', $filter['to'])
            ->orderByDesc('total')
            ->groupBy('categories.name', 'categories.id')
            ->get()
            ->toArray();
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
        return $this->entry
            ->with('invoice.card')
            ->with('category')
            ->where('category_id', $category_id)
            ->where('date', '>=', $from)
            ->where('date', '<=', $to)
            ->orderBy('date')
            ->get()
            ->toArray();
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
        $month      = $new_date->format('m');
        $year       = $new_date->format('Y');

        $total = $this->entry
            ->join('categories', 'categories.id', '=', 'invoice_entries.category_id')
            ->where('categories.type', $categoryType)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->sum('value');
               
        return $total / 100;
    }
}
