<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Core\Eloquent\CardRepository;
use App\Repositories\Core\Eloquent\ParcelRepository;
use App\Repositories\Core\Eloquent\AccountRepository;
use App\Repositories\Core\Eloquent\InvoiceRepository;
use App\Repositories\Core\Eloquent\CategoryRepository;
use App\Repositories\Interfaces\CardRepositoryInterface;
use App\Repositories\Core\Eloquent\AccountEntryRepository;
use App\Repositories\Core\Eloquent\InvoiceEntryRepository;
use App\Repositories\Interfaces\ParcelRepositoryInterface;
use App\Repositories\Interfaces\AccountRepositoryInterface;
use App\Repositories\Interfaces\InvoiceRepositoryInterface;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Repositories\Core\Eloquent\AccountsSchedulingRepository;
use App\Repositories\Interfaces\AccountEntryRepositoryInterface;
use App\Repositories\Interfaces\InvoiceEntryRepositoryInterface;
use App\Repositories\Interfaces\AccountsSchedulingRepositoryInterface;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            CategoryRepositoryInterface::class,
            CategoryRepository::class
        );

        $this->app->bind(
            AccountRepositoryInterface::class,
            AccountRepository::class
        );

        $this->app->bind(
            CardRepositoryInterface::class,
            CardRepository::class
        );

        $this->app->bind(
            InvoiceRepositoryInterface::class,
            InvoiceRepository::class
        );

        $this->app->bind(
            InvoiceEntryRepositoryInterface::class,
            InvoiceEntryRepository::class
        );

        $this->app->bind(
            ParcelRepositoryInterface::class,
            ParcelRepository::class
        );

        $this->app->bind(
            AccountEntryRepositoryInterface::class,
            AccountEntryRepository::class
        );

        $this->app->bind(
            AccountsSchedulingRepositoryInterface::class,
            AccountsSchedulingRepository::class
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
