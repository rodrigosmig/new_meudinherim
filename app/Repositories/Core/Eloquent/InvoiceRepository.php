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
        $date           = (new DateTime($date));
        $due_date       = $this->changeDayOfADate($date, $card->pay_day);
        $closing_date   = $this->changeDayOfADate($date, $card->closing_day);

        if ($card->closing_day > $date->format('t')) { 
            $closing_date = $this->changeDayOfADate($date, $date->format('t'));
        }

        if ($due_date < $closing_date) {
            $due_date->modify('+1 month');
        }

        if ($card->closing_day <= $date->format('d')) {
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
    public function getInvoicesByStatus($card, $paid = false,  $per_page = 10)
    {
        return $card->invoices()
                ->where('paid', $paid)
                ->orderBy('due_date')
                ->paginate($per_page);
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

        $parcels = $invoice->parcels()->where('anticipated', false)->get();

        foreach ($parcels as $parcel) {
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

    /**
     * Returns the amount of open invoices for a api chart
     *
     * @param int $card_id
     * @param string $month
     * @param string $year
     * @return int
     */
    public function getInvoicesAmountForChart($card_id, $month, $year) {
        return $this->model::join('cards', 'cards.id', '=', 'invoices.card_id')
            ->where('cards.id', '=', $card_id)
            ->whereMonth('due_date', $month)
            ->whereYear('due_date', $year)
            ->sum('amount');
    }

    /**
     * Returns the amount of open invoices for a web chart
     *
     * @param string $card_name
     * @param string $month
     * @param string $year
     * @return int
     */
    public function getInvoiceAmountForWebChart($card_name, $month, $year) {
        return $this->model::join('cards', 'cards.id', '=', 'invoices.card_id')
            ->where('cards.name', '=', $card_name)
            ->whereMonth('due_date', $month)
            ->whereYear('due_date', $year)
            ->sum('amount');
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
     * Returns the first open invoice
     *
     * @param Card $card
     * @param bool $paid 
     * @return Invoice | null
     */
    public function getTheFirstOpenInvoice($card, $paid = false) {
        return $card->invoices()
            ->where('paid', $paid)
            ->orderBy('due_date')
            ->first();
    }

    /**
     * Returns the total of open invoices for a given card and given range date
     *
     * @param Card $card
     * @param array $range_date 
     * @return int
     */
    public function getTotalOfOpenInvoice($card, $range_date): int {
        return $card->invoices()
            ->whereBetween('due_date', [$range_date['from'], $range_date['to']])
            ->where('paid', false)
            ->whereNotIn('id', function ($query) {
                $query->select('invoice_id')
                    ->from('accounts_schedulings')
                    ->whereColumn('accounts_schedulings.invoice_id', 'invoices.id');
            })
            ->sum('amount');
    }

    /**
     * Update the due date and the closing date of all open invoices
     *
     * @param Card $card
     * @return void
     */
    public function updateOpenInvoicesDate($card): void {
        $invoices = $this->getInvoicesByStatus($card, false, 100);

        $now = now();

        foreach ($invoices as $invoice) {
            if ($invoice->isClosed()) {
                continue;
            }

            $invoice_dates = $this->getDueAndClosingDateForInvoice($card, $now->format('Y-m-d'));

            $data = [
                'due_date'      => $invoice_dates['due_date']->format('Y-m-d'),
                'closing_date'  => $invoice_dates['closing_date']->format('Y-m-d')
            ];

            $this->update($invoice, $data);

            $now->modify("+1 month");
        }
    }

    /**
     * Changes the day of a given date
     *
     * @param DateTime $date
     * @param int $new_day
     * @return DateTime
     */
    private function changeDayOfADate(DateTime $date, int $new_day): DateTime {
        $new_date   = getdate($date->getTimestamp());        
        return new DateTime($new_date['year'] . '-' . $new_date['mon'] . '-' . $new_day);
    }
}
