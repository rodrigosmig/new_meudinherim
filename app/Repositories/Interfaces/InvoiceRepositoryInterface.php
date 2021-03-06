<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Model;

interface InvoiceRepositoryInterface
{
    public function createInvoice($card, string $date);
    public function getDueAndClosingDateForInvoice($card, $date): array;
    public function getInvoiceByDate($card, $date);
    public function getInvoiceById($card, $invoice_id);
    public function getInvoiceTotalAmount($invoice): float;
}
