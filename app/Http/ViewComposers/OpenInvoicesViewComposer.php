<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Services\InvoiceService;


class OpenInvoicesViewComposer
{
    /**
     * The card repository implementation.
     *
     * @var array
     */
    protected $invoices;

    /**
     * Create a new cards composer.
     *
     * @param  InvoiceService  $service
     * @return void
     */
    public function __construct(InvoiceService $service)
    {
        $this->invoices = $service->getOpenInvoicesForMenu();
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $view->with('all_open_invoices', $this->invoices);
    }
}