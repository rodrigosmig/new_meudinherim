<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Services\CardService;

class CardsViewComposer
{
    /**
     * The card repository implementation.
     *
     * @var array
     */
    protected $cards;

    /**
     * Create a new cards composer.
     *
     * @param  CardService  $users
     * @return void
     */
    public function __construct(CardService $service)
    {
        $this->cards = $service->getCardsForForm();
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $view->with('form_cards', $this->cards);
    }
}