<?php

namespace App\Services;

use DateTime;
use App\Models\Card;
use App\Models\Invoice;
use App\Models\InvoiceEntry;

class InvoiceService
{
    protected $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function store(array $data)
    {
        return $this->invoice->create($data);
    }

    public function update($id, array $data)
    {
        $invoice = $this->findById($id);

        if (! $invoice) {
            return false;
        }

        return $invoice->update($data);
    }

    public function delete($id)
    {
        $invoice = $this->findById($id);

        if (! $invoice) {
            return false;
        }

        return $invoice->delete();
    }

    public function findById($id)
    {
        return $this->invoice->find($id);
    }

    /**
     * Returns invoices by status
     *
     * @param bool $paid
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getAllInvoicesByStatus($paid = false)
    {
        return $this->invoice::where('paid', $paid)
                    ->get();
    }

    /**
     * Creates a entry for the invoice
     *
     * @return InvoiceEntry
     */
    public function createEntry(Invoice $invoice, array $data): ?InvoiceEntry
    {
        $data['user_id'] = auth()->user()->id;

        if (isset($data['installment']) && isset($data['installments_number']) && $data['installments_number'] > 1) {
            for ($i = 0; $i < $data['installments_number']; $i++) { 
                var_dump("teste");
            }
        } else {
            $entry = $invoice->entries()->create($data);
        }
        
        if ($entry) {
            $this->updateInvoiceAmount($invoice);
        }
           
        return $entry;
    }

    /**
     * Updates invoice amount
     *
     * @param Invoice $invoice
     * @return bool
     */  
    /* public function updateInvoiceAmount(Invoice $invoice): bool
    {
        $total = $this->getInvoiceTotalAmount($invoice);

        return $invoice->update(['amount' => $total]);
    } */

    /* public function getInvoiceTotalAmount(Invoice $invoice): float
    {
        $total = 0;

        foreach ($invoice->entries as $entry) {
            if ($entry->category->type === $entry->category::INCOME) {
                $total -= $entry->value;
            } else {
                $total += $entry->value;
            }
        }

        return $total;
    } */

    /**
     * Returns an array with the total invoices of the lasts six months
     * The array key represents the month number
     *
     * @return array
     */ 
    public function getTotalInvoicesForSixMonthsForChart($date): array
    {
        $result     = [];
        $cards_name = Card::pluck('name');

        foreach ($cards_name as $card) {
            $new_date   = (new DateTime($date))->modify('-5 months');

            for ($i=0; $i < 6; $i++) { 
                $month  = $new_date->format('m');
                $year   = $new_date->format('Y');

                $total = $this->invoice
                    ->join('cards', 'cards.id', '=', 'invoices.card_id')
                    ->where('cards.name', '=', $card)
                    ->whereMonth('due_date', $month)
                    ->whereYear('due_date', $year)
                    ->sum('amount');

                $result[$card][] = $total / 100;
                                
                $new_date = $new_date->modify("+1 month");
            }

        }

        return $result;
    }

    /**
     * Returns the most recent open invoice
     *
     * @return array
     */ 
    public function getOpenInvoicesForMenu(): array
    {
        $cards  = auth()->user()->cards;
        $result = [];
        $total  = 0;

        foreach ($cards as $card) {            
            $invoice = $card->invoices()
                ->where('paid', false)
                ->orderBy('due_date')
                ->first();
            
            $result[$card->name] = $invoice;
            $total += $invoice->amount;
        }

        $result['total'] = $total;
        
        return $result;
    }
}
