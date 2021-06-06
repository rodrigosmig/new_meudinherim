<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CardService;
use Illuminate\Http\Response;
use App\Services\InvoiceEntryService;
use RealRashid\SweetAlert\Facades\Alert;
use App\Exceptions\InsufficientLimitException;
use App\Http\Requests\AnticipateParcelsRequest;
use App\Http\Requests\StoreInvoiceEntryRequest;
use App\Http\Requests\UpdateInvoiceEntryRequest;
use App\Repositories\Interfaces\InvoiceRepositoryInterface;

class InvoiceEntryController extends Controller
{
    /* The InvoiceEntryService instance.
	 *
	 * @var InvoiceEntryService
	 */
    private $service;

    /* The CardService instance.
	 *
	 * @var CardService
	 */
    private $cardService;

    public function __construct(
        InvoiceEntryService $service, 
        CardService $cardService, 
        InvoiceRepositoryInterface $invoiceRepository
    ) {
        $this->service              = $service;
        $this->cardService          = $cardService;
        $this->invoiceRepository    = $invoiceRepository;

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

        if (!$card) {
            Alert::error(__('global.invalid_request'), __('messages.entries.not_found'));
            return redirect()->route('cards.index');
        }

        $invoice = $this->cardService->getInvoiceById($card, $invoice_id);

        if (!$invoice) {
            Alert::error(__('global.invalid_request'), __('messages.invoices.not_found'));
            return redirect()->route('cards.index');
        }

        $data = [
            'title'     => __('global.invoice'),
            'entries'   => $this->service->getAllEntriesForInvoice($invoice),
            'invoice'   => $invoice
        ];

        return view('invoice_entries.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [
            'title' => $this->title,
        ];

        return view('invoice_entries.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreInvoiceEntryRequest $request)
    {
        $data = $request->validated();
        $card = $this->cardService->findById($data['card_id']);

        if (!$card) {
            Alert::error(__('global.invalid_request'), __('messages.entries.not_found'));
            return redirect()->route('cards.index');
        }

        try {
            $this->service->create($card, $data);
        } catch (InsufficientLimitException $exception) {
            Alert::error(__('global.invalid_request'), $exception->getMessage());
            return redirect()->back();
        }

        Alert::success(__('global.success'), __('messages.entries.create'));
        return redirect()->route('cards.invoices.index', $card->id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $entry = $this->service->findById($id);

        if (! $entry) {
            Alert::error(__('global.invalid_request'), __('messages.entries.not_found'));
            return redirect()->route('cards.index');
        }

        return view('invoice_entries.edit', [
            'title' => $this->title,
            'entry' => $entry
        ]);
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
        $entry  = $this->service->findById($id);

        if (! $entry) {
            Alert::error(__('global.invalid_request'), __('messages.entries.not_found'));
            return redirect()->route('cards.index');
        }

        try {
            $this->service->update($entry, $data);
        } catch (InsufficientLimitException $exception) {
            Alert::error(__('global.invalid_request'), $exception->getMessage());
            return redirect()->back();
        }

        $this->invoiceRepository->updateInvoiceAmount($entry->invoice);
        $this->cardService->updateCardBalance($entry->invoice->card);

        Alert::success(__('global.success'), __('messages.entries.update'));

        return redirect()->route('invoice_entries.index', [$entry->invoice->card->id, $entry->invoice->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $entry = $this->service->findById($id);

        if (! $entry) {
            Alert::error(__('global.invalid_request'), __('messages.entries.not_found'));
            return redirect()->route('cards.index');
        }

        $invoice = $entry->invoice;

        if (! $this->service->delete($entry)) {
            return response()
                    ->json(['title' => __('global.invalid_request'), 'text' => __('messages.not_delete')]);
        }

        $this->invoiceRepository->updateInvoiceAmount($entry->invoice);
        $this->cardService->updateCardBalance($invoice->card);

        return response()
                ->json(['title' => __('global.success'), 'text' => __('messages.entries.delete')]);
    }

    public function ajaxNextParcels()
    {
        $card_id        = request()->get('card_id', 0);
        $parcel_number  = request()->get('parcel_number', 0);
        $entry_id       = request()->get('entry_id', 0);

        $card = $this->cardService->findById($card_id);

        if (!$card) {
            return response()
                ->json(['title' => __('global.invalid_request'), 'text' => __('messages.not_found')], Response::HTTP_NOT_FOUND);
        }

        $entry = $this->service->findById($entry_id);

        if (!$entry) {
            return response()
                ->json(['title' => __('global.invalid_request'), 'text' => __('messages.not_found')], Response::HTTP_NOT_FOUND);
        }

        if (!$entry->has_parcels) {
            return response()
                ->json(['title' => __('global.invalid_request'), 'text' => __('messages.entries.not_parcel')], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $parcels = $this->service->getOpenParcels($entry, $parcel_number);

        return response()->json([
            'total'     => $entry->value,
            'parcels'   => $parcels
        ]);
    }

    public function anticipateParcels(AnticipateParcelsRequest $request, $entry_id)
    {
        $entry = $this->service->findById($entry_id);

        if (!$entry) {
            Alert::error(__('global.invalid_request'), __('messages.parcels.not_found'));
            return redirect()->route('cards.index');
        }

        if (!$entry->has_parcels) {
            Alert::error(__('global.invalid_request'), __('messages.entries.not_parcel'));
            return redirect()->back();
        }

        $parcels_ids = $request->parcels;

        if (! $this->service->parcelsExists($entry, $parcels_ids)) {
            Alert::error(__('global.invalid_request'), __('messages.parcels.not_found'));
            return redirect()->route('cards.index');
        }

        $card = $entry->invoice->card;
        
        $this->service->anticipateParcels($card, $parcels_ids);

        Alert::success(__('global.success'), __('messages.parcels.anticipate'));

        return redirect()->back();
    }
}
