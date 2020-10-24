<?php

namespace App\Http\Controllers;


use App\Services\InvoiceService;

class InvoiceController extends Controller
{
    /* The InvoiceService instance.
	 *
	 * @var InvoiceService
	 */
    private $service;

    public function __construct(InvoiceService $service)
    {
        $this->service = $service;
        $this->title = __('global.invoices');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            'title'     => $this->title,
            'open_invoices'  => $this->service->getAllInvoicesByStatus(),
            'paid_invoices'  => $this->service->getAllInvoicesByStatus($paid = true),
        ];

        return view('invoices.index', $data);
    }
}
