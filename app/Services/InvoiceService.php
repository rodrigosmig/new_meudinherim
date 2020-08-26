<?php

namespace App\Services;

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
        $data['user_id'] = auth()->user()->id;

        return $this->invoice->create($data);
    }

    public function update($id, array $data)
    {
        $invoice = $this->findById($id);

        return $invoice->update($data);
    }

    public function delete($id)
    {
        $invoice = $this->findById($id);

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
                    ->where('user_id', auth()->user()->id)
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

        dd($data, isset($data['installment']));
        
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
    public function updateInvoiceAmount(Invoice $invoice): bool
    {
        $total = $this->getInvoiceTotalAmount($invoice);

        return $invoice->update(['amount' => $total]);
    }

    public function getInvoiceTotalAmount(Invoice $invoice): float
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
    }
}
