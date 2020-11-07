<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Http\ViewComposers\CardsViewComposer;
use App\Http\ViewComposers\AccountsViewComposer;
use App\Http\ViewComposers\OpenInvoicesViewComposer;
use App\Http\ViewComposers\AllCategoriesViewComposer;
use App\Http\ViewComposers\AccountBalanceViewComposer;
use App\Http\ViewComposers\IncomeCategoriesViewComposer;
use App\Http\ViewComposers\ExpenseCategoriesViewComposer;
use App\Http\ViewComposers\UserNotificationsViewComposer;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {      
        View::composer([
            'invoice_entries.create'
        ], CardsViewComposer::class);

        View::composer([
            'account_entries.create',
            'payables.show',
            'receivables.show',
            'accounts.transfer'
        ], AccountsViewComposer::class);

        View::composer([
            'invoice_entries.create',
            'invoice_entries.edit',
            'account_entries.create',
            'account_entries.edit'
        ], AllCategoriesViewComposer::class);

        View::composer([
            'receivables.create',
            'receivables.edit',
            'accounts.transfer'
        ], IncomeCategoriesViewComposer::class);

        View::composer([
            'payables.create',
            'payables.edit',
            'accounts.transfer',
            'cards.invoices.payable'
        ], ExpenseCategoriesViewComposer::class);

        View::composer([
            'includes.balance_menu'
        ], AccountBalanceViewComposer::class);

        View::composer([
            'includes.open_invoices_menu'
        ], OpenInvoicesViewComposer::class);

        View::composer([
            'includes.notifications'
        ], UserNotificationsViewComposer::class);
    }
}
