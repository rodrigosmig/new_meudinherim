<?php

namespace App\Services;

use DateTime;
use App\Models\Card;
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
    {
        $this->card = $card;
        $this->data = $data;

        if ($this->data['value'] > $this->card->balance) {
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
     *
     * @return bool
     */ 
    public function createEntry(): bool
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

        return true;
    }

    /**
     * Create the installments
     *
     * @return bool
     */ 
    public function createInstallments(): bool
    {
        $date                   = new DateTime($this->data['date']);
        $total                  = $this->data['value'];
        $installments_number    = $this->data['installments_number'];
        $installment_value      = number_format($total / $installments_number, 2);
        $description_default    = $this->data['description'];

        $this->data['value'] = $installment_value;

        for ($i = 1; $i <= $installments_number; $i++) {
            $this->data['date']           = $date->format('Y-m-d');
            $this->data['description']    = $description_default . " {$i}/{$installments_number}" ;           

            $entry = $this->createEntry();

            if (! $entry) {
                return false;
            }

            $date = $date->modify('+1 month');
        }

        return true;
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

    
}
