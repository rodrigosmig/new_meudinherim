<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Http\ViewComposers\CardsViewComposer;
use App\Http\ViewComposers\AccountsViewComposer;
use App\Http\ViewComposers\AllCategoriesViewComposer;

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
    }
}
