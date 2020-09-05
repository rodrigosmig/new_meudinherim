<?php

namespace App\Providers;

use App\Models\Card;
use App\Models\Account;
use App\Models\Invoice;
use App\Models\Category;
use App\Models\AccountEntry;
use App\Models\InvoiceEntry;
use App\Observers\CardObserver;
use App\Observers\AccountObserver;
use App\Observers\InvoiceObserver;
use App\Observers\CategoryObserver;
use App\Observers\AccountEntryObserver;
use App\Observers\InvoiceEntryObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Category::observe(CategoryObserver::class);
        Account::observe(AccountObserver::class);
        Card::observe(CardObserver::class);
        Invoice::observe(InvoiceObserver::class);
        InvoiceEntry::observe(InvoiceEntryObserver::class);
        AccountEntry::observe(AccountEntryObserver::class);
    }
}
