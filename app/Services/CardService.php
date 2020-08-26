<?php

namespace App\Services;

use DateTime;
use App\Models\Card;
use App\Models\Invoice;
use App\Exceptions\InsufficientLimitException;

class CardService
{
    protected $card, $invoiceService;

    public function __construct(Card $card, InvoiceService $invoiceService)
    {
        $this->card = $card;
        $this->invoiceService = $invoiceService;
    }

    public function store(array $data)
    {
        $data['user_id'] = auth()->user()->id;

        return $this->card->create($data);
    }

    public function update($id, array $data)
    {
        $card = $this->findById($id);

        $result = $card->update($data);

        if ($result) {
            $this->updateCardBalance($card);
        }

        return $result;
    }

    public function delete($id)
    {
        $card = $this->findById($id);

        return $card->delete();
    }

    public function findById($id)
    {
        return $this->card->find($id);
    }

    /**
     * Create card and the first invoice
     *
     * @param array $data
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function createCardAndInvoice($data) 
    {
        $data['balance'] = $data['credit_limit'];

        $card = $this->store($data);

        if (! $card) {
            return false;
        }

        $this->createInvoice($card, (new DateTime())->format('Y-m-d'));

        return $card;
    }

    /**
     * Create invoice
     *
     * @param Card $card
     * @param string $date
     * @return Invoice
     */
    public function createInvoice(Card $card, $date): Invoice
    {
        $date = $this->getDueAndClosingDateForInvoice($card, $date);

        $data = [
            'amount'        => 0,
            'user_id'       => auth()->user()->id,
            'due_date'      => $date['due_date'],
            'closing_date'  => $date['closing_date']
        ];

        return $card->invoices()->create($data);
    }

    /**
     * Get user cards
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getCardsByUser() 
    {
        return $this->card::where('user_id', auth()->user()->id)->get();
    }

    /**
     * Get a list of cards for the form
     *
     * @return array
     */
    public function getCardsForForm()
    {
        return $this->card::where('user_id', auth()->user()->id)->pluck('name', 'id');
    }

     /**
     * Get the invoice for the informed date
     *
     * @param Card $card
     * @param string $date
     * 
     * @return Invoice
     */
    public function getInvoiceByDate(Card $card, $date): ?Invoice
    {
        $new_date = $this->getDueAndClosingDateForInvoice($card, $date);
        
        $invoice = $card->invoices()
            ->where('closing_date', $new_date['closing_date'])
            ->where('due_date', $new_date['due_date'])
            ->where('paid', false)
            ->orderBy('closing_date', 'ASC')
            ->first();       

        if (! $invoice && (new DateTime($date)) > now()) {
            $invoice = $this->createInvoice($card, $date);
        }

        return $invoice;
    }

    /**
     * Get invoice by id
     *
     * @param Card $card
     * @param int $invoice_idfirst
     * 
     * @return Invoice
     */
    public function getInvoiceById(Card $card, $invoice_id): ?Invoice
    {
        return $card->invoices()
            ->where('id', $invoice_id)
            ->where('user_id', auth()->user()->id)
            ->first();
    }

    /**
     * Returns invoices for a given status
     *
     * @param Card $card
     * @param bool $paid
     * 
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getInvoicesByStatus(Card $card, $paid = false)
    {
        return $card->invoices()
            ->where('paid', $paid)
            ->where('user_id', auth()->user()->id)
            ->get();
    }

    /**
     * Updates card balance
     *
     * @param Card $card
     * @return bool
     */  
    public function updateCardBalance(Card $card): bool
    {
        $invoices = $this->getInvoicesByStatus($card);

        $total = 0;
        
        foreach ($invoices as $invoice) {
            $total += $this->invoiceService->getInvoiceTotalAmount($invoice);
        }

        $balance = $card->credit_limit - $total;

        return $card->update(['balance' => $balance]);
    }

    /**
     * Returns an array with the invoice closing and due date based on the given date
     *
     * @param Card $card
     * @param string $date
     * @return array
     */ 
    private function getDueAndClosingDateForInvoice(Card $card, $date): array
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
     * Add the entries to the invoice
     *
     * @param Card $card
     * @param array $data
     * @return array
     */ 
    public function addInvoiceEntry(Card $card, $data)
    {
        $data['user_id'] = auth()->user()->id;

        if ($data['value'] > $card->balance) {
            throw new InsufficientLimitException(__('messages.entries.insufficient_limit'));
        }

        if (isset($data['installment']) && isset($data['installments_number']) && $data['installments_number'] > 1) {
            return $this->createInstallments($card, $data);
        } else {
            return $this->createEntry($card, $data);
        }
    }


    /**
     * Create the invoice entry
     *
     * @param Card $card
     * @param array $data
     * @return bool
     */ 
    public function createEntry(Card $card, $data): bool
    {
        $invoice = $this->getInvoiceByDate($card, $data['date']);

        if (! $invoice) {
            return false;
        }

        $entry = $invoice->entries()->create($data);

        if (! $entry) {
            return false;
        }

        $this->invoiceService->updateInvoiceAmount($invoice);

        return true;
    }

    /**
     * Create the installments
     *
     * @param Card $card
     * @param array $data
     * @return bool
     */ 
    public function createInstallments(Card $card, $data): bool
    {
        $date                   = new DateTime($data['date']);
        $total                  = $data['value'];
        $installments_number    = $data['installments_number'];
        $installment_value      = number_format($total / $installments_number, 2);
        $description_default    = $data['description'];

        $data['value'] = $installment_value;

        for ($i = 1; $i <= $installments_number; $i++) {
            $data['date']           = $date->format('Y-m-d');
            $data['description']    = $description_default . " {$i}/{$installments_number}" ;           

            $entry = $this->createEntry($card, $data);

            if (! $entry) {
                return false;
            }

            $date = $date->modify('+1 month');
        }

        return true;
    }
}
