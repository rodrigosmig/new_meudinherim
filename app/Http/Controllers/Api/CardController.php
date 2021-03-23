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
}
