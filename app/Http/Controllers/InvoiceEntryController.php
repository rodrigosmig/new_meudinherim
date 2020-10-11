<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CardService;
use App\Services\InvoiceService;
use App\Services\InvoiceEntryService;
use RealRashid\SweetAlert\Facades\Alert;
use App\Exceptions\InsufficientLimitException;
use App\Http\Requests\StoreInvoiceEntryRequest;
use App\Http\Requests\UpdateInvoiceEntryRequest;
use App\Http\Requests\StoreUpdateInvoiceEntryRequest;

class InvoiceEntryController extends Controller
{
    /* The InvoiceService instance.
	 *
	 * @var InvoiceEntryService
	 */
    private $service;

    /* The InvoiceService instance.
	 *
	 * @var InvoiceService
	 */
    private $invoiceService;
    
    /* The CardService instance.
	 *
	 * @var CardService
	 */
    private $cardService;

    public function __construct(InvoiceEntryService $service, CardService $cardService, InvoiceService $invoiceService)
    {
        $this->middleware(['auth', 'verified']);

        $this->service          = $service;
        $this->cardService      = $cardService;
        $this->invoiceService   = $invoiceService;

        $this->title = __('global.invoice_entry');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($invoice_id, $card_id)
    {
        $card = $this->cardService->findById($card_id);

        if (!$card) {
            Alert::error(__('global.invalid_request'), __('messages.entries.not_found'));
            return redirect()->route('cards.index');
        }

        $invoice = $this->cardService->getInvoiceById($card, $invoice_id);

        if (!$invoice) {
            Alert::error(__('global.invalid_request'), __('messages.entries.not_found'));
            return redirect()->route('cards.index');
        }

        $data = [
            'title'     => __('global.invoice'),
            'entries'   => $invoice->entries,
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
        $data = $request->except('_token');
        $card = $this->cardService->findById($data['card_id']);

        try {
            $entry = $this->service->make($card, $data);
        } catch (InsufficientLimitException $exception) {
            Alert::error(__('global.invalid_request'), $exception->getMessage());
            return redirect()->back();
        }

        if (! $entry) {
            Alert::warning(__('global.invalid'), __('messages.not_save'));
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
        try {
            $entry = $this->service->update($id, $request->all());
        } catch (InsufficientLimitException $exception) {
            Alert::error(__('global.invalid_request'), $exception->getMessage());
            return redirect()->back();
        }

        if (! $entry) {
            Alert::error(__('global.invalid_request'), __('messages.not_save'));
            return redirect()->route('cards.index');
        }
        
        $this->invoiceService->updateInvoiceAmount($entry->invoice);
        $this->cardService->updateCardBalance($entry->invoice->card);

        Alert::success(__('global.success'), __('messages.entries.update'));

        return redirect()->route('invoice_entries.index', [$entry->invoice->id, $entry->invoice->card->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $entry      = $this->service->findById($id);

        if (! $entry) {
            Alert::error(__('global.invalid_request'), __('messages.entries.not_found'));
            return redirect()->route('cards.index');
        }

        $invoice    = $entry->invoice;

        if (! $this->service->delete($id)) {
            return response()
                    ->json(['title' => __('global.invalid_request'), 'text' => __('messages.not_delete')]);
        }

        $this->invoiceService->updateInvoiceAmount($invoice);

        return response()
                ->json(['title' => __('global.success'), 'text' => __('messages.entries.delete')]);
    }
}
