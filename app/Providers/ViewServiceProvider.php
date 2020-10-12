<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Http\ViewComposers\CardsViewComposer;
use App\Http\ViewComposers\AccountsViewComposer;
use App\Http\ViewComposers\AllCategoriesViewComposer;
use App\Http\ViewComposers\IncomeCategoriesViewComposer;
use App\Http\ViewComposers\ExpenseCategoriesViewComposer;

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
            'account_entries.create'
        ], AccountsViewComposer::class);

        View::composer([
            'invoice_entries.create',
            'invoice_entries.edit',
            'account_entries.create',
            'account_entries.edit'
        ], AllCategoriesViewComposer::class);

        View::composer([
            'accounts_scheduling.create',
            'accounts_scheduling.edit',
        ], IncomeCategoriesViewComposer::class);

        View::composer([
            'payables.create',
            'payables.edit',
        ], ExpenseCategoriesViewComposer::class);
    }
}
