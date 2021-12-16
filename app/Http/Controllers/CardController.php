<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CardService;
use Illuminate\Http\Response;
use Illuminate\Database\QueryException;
use RealRashid\SweetAlert\Facades\Alert;
use App\Http\Requests\StoreUpdateCardRequest;

class CardController extends Controller
{
    /* The CardService instance.
	 *
	 * @var CardService
	 */
    private $service;
    
    public function __construct(CardService $service)
    {
        $this->service = $service;
        $this->title = __('global.credit-card');
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
            'cards'  => $this->service->getCards(),

        ];

        return view('cards.index', $data);
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

        return view('cards.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUpdateCardRequest $request)
    {
        $data = $request->validated();

        $card = $this->service->create($data);

        if (! $card) {
            Alert::error(__('global.invalid_request'), __('messages.not_save'));
            return redirect()->route('cards.index');
        }

        Alert::success(__('global.success'), __('messages.cards.create'));

        return redirect()->route('cards.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $card = $this->service->findById($id);

        if (! $card) {
            Alert::error(__('global.invalid_request'), __('messages.cards.not_found'));
            return redirect()->route('cards.index');
        }

        return view('cards.edit', [
            'card'  => $card,
            'title'     => $this->title
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreUpdateCardRequest $request, $id)
    {
        $data       = $request->validated();
        $card    = $this->service->findById($id);

        if (! $card) {
            Alert::error(__('global.invalid_request'), __('messages.cards.not_found'));
            return redirect()->route('accounts.index');
        }

        $this->service->update($card, $data);

        Alert::success(__('global.success'), __('messages.cards.update'));

        return redirect()->route('cards.index');
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
            return response()
                    ->json(['title' => __('global.invalid_request'), 'text' => __('messages.cards.not_found')], Response::HTTP_NOT_FOUND);
        }

        try {
            $this->service->delete($card);
        } catch (QueryException $e) {
            return response()
                    ->json(['title' => __('global.invalid_request'), 'text' => __('messages.cards.not_delete')], Response::HTTP_BAD_REQUEST);
        }

        return response()
                ->json(['title' => __('global.success'), 'text' => __('messages.cards.delete')]);
    }

    public function invoices($card_id)
    {
        $card = $this->service->findById($card_id);

        if (! $card) {
            Alert::error(__('global.invalid_request'), __('messages.cards.not_found'));
            return redirect()->route('cards.index');
        }

        $data = [
            'title'     => __('global.invoices'),
            'open_invoices'  => $this->service->getInvoicesByStatus($card, false, 1000),
            'paid_invoices'  => $this->service->getInvoicesByStatus($card, $paid = true, 1000),
            'card' => $card
        ];

        return view('cards.invoices.index', $data);
    }

    public function generatePayment($card_id, $invoice_id)
    {
        $card = $this->service->findById($card_id);

        if (! $card) {
            Alert::error(__('global.invalid_request'), __('messages.cards.not_found'));
            return redirect()->route('cards.index');
        }

        $invoice = $this->service->getInvoiceById($card, $invoice_id);

        if (! $invoice) {
            Alert::error(__('global.invalid_request'), __('messages.invoices.not_found'));
            return redirect()->route('cards.index');
        }

        $data = [
            'title' => 'Gerar fatura',
            "due_date" => $invoice->due_date,
            "description" => __('global.invoice') . ": " . $invoice->card->name,
            "value" => $invoice->amount,
            "invoice_id" => $invoice->id,
        ];

        return view('cards.invoices.payable', $data);
    }
}
