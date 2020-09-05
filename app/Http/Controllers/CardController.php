<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CardService;
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
        $this->middleware(['auth', 'verified', 'cardOwner']);

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
            'cards'  => $this->service->getCardsByUser(),

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
        $card = $this->service->make($request->all());

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
        if (! $this->service->update($id, $request->all())) {
            Alert::error(__('global.invalid_request'), __('messages.not_save'));

            return redirect()->route('cards.index');
        }

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
        if (! $this->service->delete($id)) {
            return response()
                    ->json(['title' => __('global.invalid_request'), 'text' => __('messages.not_delete')]);
        }

        return response()
                ->json(['title' => __('global.success'), 'text' => __('messages.cards.delete')]);
    }

    public function invoices($card_id)
    {
        $card = $this->service->findById($card_id);

        $data = [
            'title'     => __('global.invoices'),
            'open_invoices'  => $this->service->getInvoicesByStatus($card),
            'paid_invoices'  => $this->service->getInvoicesByStatus($card, $paid = true),
            'card' => $card
        ];

        return view('cards.invoices.index', $data);
    }
}
