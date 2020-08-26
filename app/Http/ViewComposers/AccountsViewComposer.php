<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Services\AccountService;

class AccountsViewComposer
{
    /**
     * The card repository implementation.
     *
     * @var array
     */
    protected $accounts;

    /**
     * Create a new accounts composer.
     *
     * @param  AccountService  $users
     * @return void
     */
    public function __construct(AccountService $service)
    {
        $this->accounts = $service->getAccountsForForm();
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $view->with('form_accounts', $this->accounts);
    }
}