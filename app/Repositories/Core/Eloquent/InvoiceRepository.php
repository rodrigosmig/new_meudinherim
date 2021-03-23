<?php

namespace App\Repositories\Core\Eloquent;

use DateTime;
use App\Models\Card;
use App\Models\Invoice;
use App\Repositories\Core\BaseEloquentRepository;
use App\Repositories\Interfaces\InvoiceRepositoryInterface;

class InvoiceRepository extends BaseEloquentRepository implements InvoiceRepositoryInterface
{
    protected $model = Invoice::class;

    public function createInvoice($card, string $date)
    {
        $date = $this->getDueAndClosingDateForInvoice($card, $date);

        $data = [
            'amount'        => 0,
            'due_date'      => $date['due_date'],
            'closing_date'  => $date['closing_date']
        ];

        return $card->invoices()->create($data);
    }

    /**
     * Returns an array with the invoice closing and due date based on the given date
     *
     * @param Card $card
     * @param string $date
     * @return array
     */ 
    public function getDueAndClosingDateForInvoice($card, $date): array
    {
        $timestamp  = (new DateTime($date))->getTimestamp();
        $new_date   = getdate($timestamp);        

        $due_date       = new DateTime($new_date['year'] . '-' . $new_date['mon'] . '-' . $card->pay_day);
        $closing_date   = new DateTime($new_date['year'] . '-' . $new_date['mon'] . '-' . $card->closing_day);

        if ($card->closing_day <= $new_date['mday']) {
            $closing_date->modify('+1 month');
        }
        
        if ($due_date <= $closing_date) {
            $due_date->modify('+1 month');
        }
        
        return [
            'due_date'      => $due_date,
            'closing_date'  => $closing_date
        ];
    }

    /**
     * Returns invoices for a given status
     *
     * @param Card $card
     * @param bool $paid
     * 
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getInvoicesByStatus($card, $paid = false)
    {
        return $card->invoices()
                ->where('paid', $paid)
                ->orderByDesc('due_date')
                ->get();
    }

    /**
     * Get the invoice for the informed date
     *
     * @param Card $card
     * @param string $date
     * 
     * @return Invoice
     */
    public function getInvoiceByDate($card, $date)
    {
        $new_date = $this->getDueAndClosingDateForInvoice($card, $date);
        
        $invoice = $card->invoices()
            ->where('closing_date', $new_date['closing_date'])
            ->where('due_date', $new_date['due_date'])
            ->where('paid', false)
            ->orderBy('closing_date', 'ASC')
            ->first();
        
        if (! $invoice && (new DateTime($date)) >= (new DateTime('today'))) {
            $invoice = $this->createInvoice($card, $date);
        }

        return $invoice;
    }

    /**
     * Get invoice by id
     *
     * @param Card $card
     * @param int $invoice_id
     * 
     * @return Invoice
     */
    public function getInvoiceById($card, $invoice_id)
    {
        return $card->invoices()
                ->where('id', $invoice_id)
                ->first();
    }
    
    public function getInvoiceTotalAmount($invoice): float
    {
        $total = 0;

        foreach ($invoice->entries as $entry) {
            if ($entry->hasParcels()) {
                continue;
            }

            if (! $entry->isExpenseCategory()) {
                $total -= $entry->value;
            } else {
                $total += $entry->value;
            }
        }

        foreach ($invoice->parcels as $parcel) {
            if (! $parcel->isExpenseCategory()) {
                $total -= $parcel->value;
            } else {
                $total += $parcel->value;
            }
        }

        return $total;
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
}
