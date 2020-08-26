<?php

namespace App\Repositories\Interfaces;

interface InvoiceRepositoryInterface
{
    public function createEntries($invoice, array $data);
    public function getAllInvoicesByStatus($paid = false);
    public function getInvoicesByStatus($card_id, $paid = false);
}