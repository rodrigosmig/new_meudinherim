<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\CardService;
use Illuminate\Http\Response;
use App\Services\InvoiceService;
use App\Http\Controllers\Controller;
use App\Http\Resources\CardResource;
use Illuminate\Database\QueryException;
use App\Http\Requests\Api\CardUpdateStoreRequest;

class CardController extends Controller
{
    public function __construct(CardService $cardService, InvoiceService $invoiceService)
    {
        $this->service          = $cardService;
        $this->invoiceService   = $invoiceService;
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

        $card = $this->service->make($data);

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

        return (new CardResource($card));
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
        $card = $this->service->update($id, $request->validated());

        if (! $card) {
            return response()->json(['message' => __('messages.cards.api_not_found')], Response::HTTP_NOT_FOUND);
        }

        return (new CardResource($this->service->findById($id)));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $card = $this->service->delete($id);
        } catch (QueryException $e) {
            return response()->json(['message' => __('messages.cards.not_delete')], Response::HTTP_NOT_FOUND);
        }

        if (! $card) {
            return response()->json(['message' => __('messages.cards.api_not_found')], Response::HTTP_NOT_FOUND);
        }

        return response()->json();
    }
}
