<?php

namespace App\Repositories\Core\Eloquent;

use App\Models\Card;
use App\Repositories\Core\BaseEloquentRepository;
use App\Repositories\Interfaces\CardRepositoryInterface;
use DateTime;

class CardRepository extends BaseEloquentRepository implements CardRepositoryInterface
{
    protected $model = Card::class;

    public function getCards() 
    {
        return auth()->user()->cards;
    }

    public function getCardsForForm()
    {
        return auth()->user()->cards()->pluck('name', 'id');
    }
}
