<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\CardService;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\CardResource;
use App\Http\Resources\InvoiceResource;
use Illuminate\Database\QueryException;
use App\Http\Requests\Api\CardUpdateStoreRequest;
use App\Services\InvoiceService;

class CardController extends Controller
{
    public function __construct(CardService $cardService)
    {
        $this->service = $cardService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cards = $this->service->getCards();

        return CardResource::collection($cards);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CardUpdateStoreRequest $request)
    {
        $data = $request->validated();

        $card = $this->service->create($data);

        return (new CardResource($card))
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
        $card = $this->service->findById($id);

        if (! $card) {
            return response()->json(['message' => __('messages.cards.api_not_found')], Response::HTTP_NOT_FOUND);
        }

        return new CardResource($card);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CardUpdateStoreRequest $request, $id)
    {
        $data = $request->validated();
        $card = $this->service->findById($id);

        if (! $card) {
            return response()->json(['message' => __('messages.cards.api_not_found')], Response::HTTP_NOT_FOUND);
        }

        $this->service->update($card, $data);

        return (new CardResource($card));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $card = $this->service->findById($id);

        if (! $card) {
            return response()->json(['message' => __('messages.cards.api_not_found')], Response::HTTP_NOT_FOUND);
        }

        try {
            $this->service->delete($card);
        } catch (QueryException $e) {
            return response()->json(['message' => __('messages.cards.not_delete')], Response::HTTP_BAD_REQUEST);
        }

        return response()->json([], Response::HTTP_NO_CONTENT);
    }

    public function invoices(Request $request, $card_id)
    {
        $card = $this->service->findById($card_id);

        if (! $card) {
            return response()->json(['message' => __('messages.cards.api_not_found')], Response::HTTP_NOT_FOUND);
        }

        $per_page = isset($request->per_page) && is_numeric(($request->per_page)) ? $request->per_page : 10;

        $paid = false;

        if (isset($request->status) && $request->status === 'paid') {
            $paid = true;
        }

        $invoices = $this->service->getInvoicesByStatus($card, $paid, $per_page);

        return InvoiceResource::collection($invoices);
    }

    public function getInvoice($card_id, $invoice_id)
    {
        $card = $this->service->findById($card_id);

        if (! $card) {
            return response()->json(['message' => __('messages.cards.api_not_found')], Response::HTTP_NOT_FOUND);
        }

        $invoice = $this->service->getInvoiceById($card, $invoice_id);

        if (! $invoice) {
            return response()->json(['message' => __('messages.invoices.not_found')], Response::HTTP_NOT_FOUND);
        }

        return new InvoiceResource($invoice);
    }

    public function getInvoiceBalance($card_id, $invoice_id)
    {
        $card = $this->service->findById($card_id);

        if (! $card) {
            return response()->json(['message' => __('messages.cards.api_not_found')], Response::HTTP_NOT_FOUND);
        }

        $invoice = $this->service->getInvoiceById($card, $invoice_id);

        if (! $invoice) {
            return response()->json(['message' => __('messages.invoices.not_found')], Response::HTTP_NOT_FOUND);
        }

        return new InvoiceResource($invoice);
    }

    public function getInvoicesForMenu()
    {
        $invoiceService = app(InvoiceService::class);

        $invoices = $invoiceService->getOpenInvoicesForApi();

        return response()->json($invoices, Response::HTTP_OK);
    }
}
