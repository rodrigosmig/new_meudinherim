<?php

namespace App\Http\ViewComposers;

use App\Services\AccountService;
use Illuminate\View\View;


class AccountBalanceViewComposer
{
    /**
     * The card repository implementation.
     *
     * @var array
     */
    protected $balances;

    /**
     * Create a new cards composer.
     *
     * @param  AccountService  $service
     * @return void
     */
    public function __construct(AccountService $service)
    {
        $this->balances = $service->getAllAccountBalances();
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $view->with('all_account_balances', $this->balances);
    }
}