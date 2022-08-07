<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PartialPaymentInvoiceRequest;
use App\Services\InvoiceService;
use Illuminate\Http\Response;

class InvoiceController extends Controller
{
    protected $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    public function partialPayment(PartialPaymentInvoiceRequest $request)
    {
        $this->invoiceService->createPartialPayment($request->validated());

        

        return response('', Response::HTTP_OK);
    }
}
