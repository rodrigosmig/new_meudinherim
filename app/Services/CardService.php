<?php

namespace App\Services;

use DateTime;
use App\Models\Card;
use App\Models\Invoice;
use App\Repositories\Core\Eloquent\CardRepository;
use App\Repositories\Core\Eloquent\InvoiceRepository;

class CardService
{
    protected $repository, $invoiceService;

    public function __construct(CardRepository $repository, InvoiceRepository $invoiceRepository)
    {
        $this->repository = $repository;
        $this->invoiceRepository = $invoiceRepository;
    }

    /**
     * Create credit card and the first invoice
     *
     * @param array $data
     * @return Card
     */
    public function create(array $data)
    {
        $data['balance'] = $data['credit_limit'];
        
        $card = $this->repository->create($data);

        if (! $card) {
            return false;
        }

        $this->invoiceRepository->createInvoice($card, (new DateTime())->format('Y-m-d'));

        return $card;
    }

    public function update(Card $card, array $data)
    {
        $this->repository->update($card, $data);

        $this->invoiceRepository->updateOpenInvoicesDate($card);

        $this->updateCardBalance($card);

        return $card;
    }

    public function delete(Card $card)
    {
        return $this->repository->delete($card);
    }

    public function findById($id)
    {
        return $this->repository->findById($id);
    }

    /**
     * Get user cards
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getCards() 
    {
        return $this->repository->getCards();
    }

    /**
     * Get a list of cards for the form
     *
     * @return array
     */
    public function getCardsForForm()
    {
        return $this->repository->getCardsForForm();
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
        return $this->invoiceRepository->getInvoiceById($card, $invoice_id);
    }

    /**
     * Returns invoices for a given status
     *
     * @param Card $card
     * @param bool $paid
     * 
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getInvoicesByStatus(Card $card, $paid = false, $per_page = 10)
    {
        return $this->invoiceRepository->getInvoicesByStatus($card, $paid, $per_page);
    }

    /**
     * Updates card balance
     *
     * @param Card $card
     * @return bool
     */  
    public function updateCardBalance(Card $card): bool
    {
        $invoices = $this->invoiceRepository->getInvoicesByStatus($card);

        $total = 0;
        
        foreach ($invoices as $invoice) {
            $total += $this->invoiceRepository->getInvoiceTotalAmount($invoice);
        }

        $balance = $card->credit_limit - $total;

        return $this->repository->update($card, ['balance' => $balance]);
    }
}
