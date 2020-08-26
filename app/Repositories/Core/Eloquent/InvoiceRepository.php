<?php

namespace App\Repositories\Core\Eloquent;

use App\Models\Invoice;
use App\Repositories\Core\BaseEloquentRepository;
use App\Repositories\Interfaces\InvoiceRepositoryInterface;

class InvoiceRepository extends BaseEloquentRepository implements InvoiceRepositoryInterface
{
    protected $model = Invoice::class;

    public function createEntries($invoice, array $data)
    {
        return $invoice->entries()->create($data);
    }

    public function getAllInvoicesByStatus($paid = false)
    {
        return $this->model::where('paid', $paid)
                    ->where('user_id', auth()->user()->id)
                    ->get();
    }

    public function getInvoicesByStatus($card_id, $paid = false)
    {
        return $this->model::where('paid', $paid)
                    ->where('card_id', $card_id)
                    ->where('user_id', auth()->user()->id)
                    ->get();
    }

    public function getInvoiceByDate($card, $date) 
    {
        return $card->invoices()
            ->where('closing_date', '>=', $date)
            ->first();
    }    
}