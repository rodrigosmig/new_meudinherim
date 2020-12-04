<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\CardService;
use Illuminate\Http\Response;
use App\Services\InvoiceService;
use App\Http\Controllers\Controller;
use App\Services\InvoiceEntryService;
use App\Http\Resources\InvoiceEntryResource;
use App\Exceptions\InsufficientLimitException;
use App\Http\Requests\Api\StoreInvoiceEntryRequest;
use App\Http\Requests\Api\UpdateInvoiceEntryRequest;

class InvoiceEntryController extends Controller
{
    private $entryService;
    private $invoiceService;
    private $cardService;

    public function __construct(InvoiceEntryService $entryService, CardService $cardService, InvoiceService $invoiceService)
    {
        $this->entryService     = $entryService;
        $this->cardService      = $cardService;
        $this->invoiceService   = $invoiceService;

        $this->title = __('global.invoice_entry');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($card_id, $invoice_id)
    {
        $card = $this->cardService->findById($card_id);

        if (! $card) {
            return response()->json(['message' => __('messages.entries.invalid_card')], Response::HTTP_NOT_FOUND);
        }
        
        $invoice = $this->cardService->getInvoiceById($card, $invoice_id);

        if (! $invoice) {
            return response()->json(['message' => __('messages.entries.invalid_invoice')], Response::HTTP_NOT_FOUND);
        }

        return InvoiceEntryResource::collection($invoice->entries);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreInvoiceEntryRequest $request, $card_id)
    {
        $data = $request->validated();
        $card = $this->cardService->findById($card_id);

        if (! $card) {
            return response()->json(['message' => __('messages.entries.invalid_card')], Response::HTTP_NOT_FOUND);
        }

        try {
            $entry = $this->entryService->make($card, $data);
        } catch (InsufficientLimitException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (! $entry) {
            return response()->json(['message' => __('messages.not_save')], Response::HTTP_BAD_REQUEST);
        }

        if (is_array($entry)) {
            return InvoiceEntryResource::collection($entry);
        }

        return (new InvoiceEntryResource($entry))
                    ->response()
                    ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $entry = $this->entryService->findById($id);

        if (! $entry) {
            return response()->json(['message' => __('messages.entries.api_not_found')], Response::HTTP_NOT_FOUND);
        }

        return new InvoiceEntryResource($entry);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateInvoiceEntryRequest $request, $id)
    {
        $data = $request->validated();

        try {
            $entry = $this->entryService->update($id, $data);
        } catch (InsufficientLimitException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (! $entry) {
            return response()->json(['message' => __('messages.entries.api_not_found')], Response::HTTP_NOT_FOUND);
        }

        $this->invoiceService->updateInvoiceAmount($entry->invoice);
        $this->cardService->updateCardBalance($entry->invoice->card);

        return (new InvoiceEntryResource($this->entryService->findById($id)));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! $this->entryService->delete($id)) {
            return response()->json(['message' => __('messages.entries.api_not_found')], Response::HTTP_NOT_FOUND);
        }

        return response()->json();
    }
}
