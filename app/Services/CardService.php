<?php

namespace App\Services;

use DateTime;
use App\Models\Card;
use App\Models\Invoice;

class CardService
{
    protected $card, $invoiceService;

    public function __construct(Card $card, InvoiceService $invoiceService)
    {
        $this->card = $card;
        $this->invoiceService = $invoiceService;
    }

    /**
     * Create credit card and the first invoice
     *
     * @param array $data
     * @return Card
     */
    public function make(array $data)
    {
        $data['balance'] = $data['credit_limit'];
        
        $card = $this->card->create($data);

        if (! $card) {
            return false;
        }

        $this->createInvoice($card, (new DateTime())->format('Y-m-d'));

        return $card;
    }

    public function update($id, array $data)
    {
        $card = $this->findById($id);

        if (! $card) {
            return false;
        }

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
    public function getCards() 
    {
        return auth()->user()->cards;
    }

    /**
     * Get a list of cards for the form
     *
     * @return array
     */
    public function getCardsForForm()
    {
        return auth()->user()->cards()->pluck('name', 'id');
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
     * Updates card balance
     *
     * @param Invoice $invoice
     * @return bool
     */  
    public function generatePayment(Invoice $invoice): bool
    {
        $invoice->payable()->create([
            "due_date" => $invoice->due_date,
            "description" => "Teste 1",
            "value" => $invoice->amount,
            "category_id" => "17",
        ]);
    }

}
