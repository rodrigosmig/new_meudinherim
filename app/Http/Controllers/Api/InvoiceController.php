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

    public function setAsPaid($invoice_id)
    {
        $invoice = $this->invoiceService->findById($invoice_id);

        if (! $invoice) {
            return response()->json(['message' => __('messages.invoices.not_found')], Response::HTTP_NOT_FOUND);
        }

        if ($invoice->isPaid()) {
            return response()->json(['message' => __('messages.invoices.is_paid')], Response::HTTP_BAD_REQUEST);
        }

        $this->invoiceService->update($invoice, ['paid' => true]);

        return response('', Response::HTTP_OK);
    }
}
