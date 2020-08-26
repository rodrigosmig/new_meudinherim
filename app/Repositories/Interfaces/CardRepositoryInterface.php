<?php

namespace App\Repositories\Interfaces;

interface CardRepositoryInterface
{
    public function getCardsByUser();
    public function getCardsForForm();
    public function createInvoice($card, $data);
    public function getInvoiceById($card, $invoice_id);
    public function getInvoiceByDate($card, $date);
}