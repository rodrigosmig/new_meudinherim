<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Core\Eloquent\CardRepository;
use App\Repositories\Core\Eloquent\AccountRepository;
use App\Repositories\Core\Eloquent\InvoiceRepository;
use App\Repositories\Core\Eloquent\CategoryRepository;
use App\Repositories\Interfaces\CardRepositoryInterface;
use App\Repositories\Core\Eloquent\InvoiceEntryRepository;
use App\Repositories\Interfaces\AccountRepositoryInterface;
use App\Repositories\Interfaces\InvoiceRepositoryInterface;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Repositories\Interfaces\InvoiceEntryRepositoryInterface;

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
