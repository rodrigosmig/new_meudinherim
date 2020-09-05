<?php

namespace App\Observers;

use App\Models\Card;

class CardObserver
{
    /**
     * Handle the card "created" event.
     *
     * @param  \App\Models\Card  $card
     * @return void
     */
    public function creating(Card $card)
    {
        $card->user_id = auth()->user()->id;
        $card->balance = $card->credit_limit;
    }

    /**
     * Handle the card "updated" event.
     *
     * @param  \App\Models\Card  $card
     * @return void
     */
    public function updating(Card $card)
    {
        $card->user_id = auth()->user()->id;
    }
}
