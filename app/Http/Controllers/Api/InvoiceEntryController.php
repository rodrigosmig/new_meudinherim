<?php

namespace App\Http\Controllers\Api;

use App\Models\InvoiceEntry;
use Illuminate\Http\Request;
use App\Services\CardService;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Services\InvoiceEntryService;
use App\Http\Resources\InvoiceEntryResource;
use App\Exceptions\InsufficientLimitException;
use App\Http\Resources\InvoiceEntryParcelResource;
use App\Http\Requests\Api\AnticipateParcelsRequest;
use App\Http\Requests\Api\StoreInvoiceEntryRequest;
use App\Http\Requests\Api\UpdateInvoiceEntryRequest;
use App\Repositories\Core\Eloquent\InvoiceRepository;

class InvoiceEntryController extends Controller
{
    private $entryService;
    private $invoiceRepository;
    private $cardService;

    public function __construct(
        InvoiceEntryService $entryService, 
        CardService $cardService, 
        InvoiceRepository $invoiceRepository
    ) {
        $this->entryService         = $entryService;
        $this->cardService          = $cardService;
        $this->invoiceRepository    = $invoiceRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $card_id, $invoice_id)
    {
        $per_page = isset($request->per_page) && is_numeric(($request->per_page)) ? $request->per_page : 10;
        $page = isset($request->page) && is_numeric($request->page) ? $request->page : 1;

        $card = $this->cardService->findById($card_id);

        if (! $card) {
            return response()->json(['message' => __('messages.entries.invalid_card')], Response::HTTP_NOT_FOUND);
        }
        
        $invoice = $this->invoiceRepository->getInvoiceById($card, $invoice_id);

        if (! $invoice) {
            return response()->json(['message' => __('messages.entries.invalid_invoice')], Response::HTTP_NOT_FOUND);
        }        

        $entries = $this->entryService->getAllEntriesForInvoice($invoice);

        $entries_collection = InvoiceEntryResource::collection($entries)->toArray($entries);

        $results = paginate($page, $per_page, $entries_collection);

        return response()->json($results);
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
            $entry = $this->entryService->create($card, $data);
        } catch (InsufficientLimitException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        if (! $entry) {
            return response()->json(['message' => __('messages.not_save')], Response::HTTP_BAD_REQUEST);
        }

        if (get_class($entry) !== InvoiceEntry::class) {
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
        $data   = $request->validated();
        $entry  = $this->entryService->findById($id);

        if (! $entry) {
            return response()->json(['message' => __('messages.entries.api_not_found')], Response::HTTP_NOT_FOUND);
        }
        
        try {
            $this->entryService->update($entry, $data);
        } catch (InsufficientLimitException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        $this->invoiceRepository->updateInvoiceAmount($entry->invoice);
        $this->cardService->updateCardBalance($entry->invoice->card);

        return (new InvoiceEntryResource($entry));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $entry = $this->entryService->findById($id);

        if (! $entry) {
            return response()->json(['message' => __('messages.entries.api_not_found')], Response::HTTP_NOT_FOUND);
        }

        $invoice = $entry->invoice;

        $this->entryService->delete($entry);

        $this->invoiceRepository->updateInvoiceAmount($invoice);

        return response()->json([], Response::HTTP_NO_CONTENT);
    }

    public function nextParcels($entry_id)
    {
        $parcel_number = request()->get('parcel_number', '');

        if (empty($parcel_number)) {
            return response()->json(['message' => __('messages.parcels.parcel_number')], Response::HTTP_BAD_REQUEST);
        }

        $entry = $this->entryService->findById($entry_id);

        if (!$entry) {
            return response()->json(['message' => __('messages.entries.api_not_found')], Response::HTTP_NOT_FOUND);
        }

        if (!$entry->has_parcels) {
            return response()->json(['message' => __('messages.entries.not_parcel')], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $parcels = $this->entryService->getOpenParcels($entry, $parcel_number);

        return InvoiceEntryParcelResource::collection($parcels);

    }

    public function anticipateParcels(AnticipateParcelsRequest $request, $entry_id)
    {
        $entry = $this->entryService->findById($entry_id);

        if (!$entry) {
            return response()->json(['message' => __('messages.entries.api_not_found')], Response::HTTP_NOT_FOUND);
        }

        if (!$entry->has_parcels) {
            return response()->json(['message' => __('messages.entries.not_parcel')], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $parcels_ids = $request->parcels;

        if (! $this->entryService->parcelsExists($entry, $parcels_ids)) {
            return response()->json(['message' => __('messages.parcels.not_found')], Response::HTTP_NOT_FOUND);
        }

        $card = $entry->invoice->card;
        
        $this->entryService->anticipateParcels($card, $parcels_ids);

        return response()->json(['message' => __('messages.parcels.anticipate')], Response::HTTP_OK);
    }
}
