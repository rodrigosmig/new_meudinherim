<?php

namespace App\Repositories\Core\Eloquent;

use App\Models\Card;
use App\Repositories\Core\BaseEloquentRepository;
use App\Repositories\Interfaces\CardRepositoryInterface;
use DateTime;

class CardRepository extends BaseEloquentRepository implements CardRepositoryInterface
{
    protected $model = Card::class;

    public function store(array $data)
    {
        $data['credit_limit'] *= 100;
        $data['user_id'] = auth()->user()->id;
        
        return parent::store($data);
    }

    public function update($id, array $data)
    {
        $data['credit_limit'] *= 100;

        return parent::update($id, $data);
    }

    public function getCardsByUser() 
    {
        return $this->model::where('user_id', auth()->user()->id)->get();
    }

    public function getCardsForForm() 
    {
        return $this->model::where('user_id', auth()->user()->id)->pluck('name', 'id');
    }

    public function createInvoice($card, $data)
    {
        return $card->invoices()->create($data);
    }

    public function getInvoiceByDate($card, $date) 
    {
        return $card->invoices()
            ->where('closing_date', '>=', $date)
            ->first();
    }

    public function getInvoiceById($card, $invoice_id) 
    {
        return $card->invoices()
            ->where('id', $invoice_id)
            ->first();
    }
}
