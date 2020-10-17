<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Services\AccountService;
use App\Http\Requests\PaymentRequest;
use RealRashid\SweetAlert\Facades\Alert;
use App\Http\Requests\StorePayableRequest;
use App\Services\AccountsSchedulingService;
use App\Exceptions\AccountsPayableIsNotPaidException;
use App\Exceptions\AccountsPayableIsAlreadyPaidException;

class PayableController extends Controller
{
    protected $service;
    protected $accountService;

    public function __construct(AccountsSchedulingService $service, AccountService $accountService)
    {
        $this->service = $service;
        $this->accountService = $accountService;
        $this->title = __('global.accounts_payable');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $range_date = null;

        if (isset($request->filter_from)
            && $request->filter_from
            && isset($request->filter_to)
            && $request->filter_to
        ) {
            $range_date = [
                'from'  => $request->filter_from,
                'to'    => $request->filter_to
            ];
        }
        //dd($range_date);
        $data = [
            'title'         => $this->title,
            'payables'       => $this->service->getCategoriesByType(Category::EXPENSE, $range_date)
        ];

        return view('payables.index', $data);
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

        return view('payables.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePayableRequest $request)
    {
        $data = $request->validated();
        
        $payable = $this->service->store($data);

        if (! $payable) {
            Alert::error(__('global.invalid_request'), __('messages.not_save'));
            return redirect()->route('accounts-scheduling.index');
        }
   
        Alert::success(__('global.success'), __('messages.account_scheduling.payable_created'));

        return redirect()->route('payables.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $payable = $this->service->findById($id);

        if (! $payable) {
            Alert::error(__('global.invalid_request'), __('messages.not_found'));
            return redirect()->route('payables.index');
        }

        return view('payables.show', [
            'title'     => $this->title,
            'payable'   => $payable
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $payable = $this->service->findById($id);

        if (! $payable) {
            Alert::error(__('global.invalid_request'), __('messages.not_found'));
            return redirect()->route('payables.index');
        }

        if ($payable->isPaid()) {
            return redirect()->route('payables.show', $payable->id);
        }

        return view('payables.edit', [
            'title'     => $this->title,
            'payable'   => $payable 
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StorePayableRequest $request, $id)
    {
        if (! $this->service->update($id, $request->all())) {
            Alert::error(__('global.invalid_request'), __('messages.not_save'));

            return redirect()->route('payables.index');
        }

        Alert::success(__('global.success'), __('messages.account_scheduling.payable_updated'));

        return redirect()->route('payables.index');
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
                ->json(['title' => __('global.success'), 'text' => __('messages.account_scheduling.payable_deleted')]);
    }

    public function payment(PaymentRequest $request, $id)
    {
        $data       = $request->validated();
        $data['id'] = $id;
        $account    = $this->accountService->findById($data['account_id']);

        try {
            $entry = $this->service->payment($account, $data);
        } catch (AccountsPayableIsAlreadyPaidException $exception) {
            Alert::error(__('global.invalid_request'), $exception->getMessage());
            return redirect()->route('payables.index');
        }

        if (! $entry) {
            Alert::error(__('global.invalid_request'), __('messages.not_found'));
            return redirect()->route('payables.index');
        }

        $this->accountService->updateBalance($account, $data['paid_date']);

        Alert::success(__('global.success'), __('messages.account_scheduling.payable_paid'));

        return redirect()->route('payables.index');
    }

    public function cancelPayment($id) {
        $payable = $this->service->findById($id);

        if (! $payable) {
            Alert::error(__('global.invalid_request'), __('messages.not_found'));
            return redirect()->route('payables.index');
        }

        try {
            $response = $this->service->cancelPayment($payable);
        } catch (AccountsPayableIsNotPaidException $exception) {
            Alert::error(__('global.invalid_request'), $exception->getMessage());
            return redirect()->route('payables.index');
        }

        if (! $response) {
            Alert::error(__('global.invalid_request'), __('messages.account_scheduling.not_cancel_payment'));
            return redirect()->route('payables.index');
        }

        Alert::success(__('global.success'), __('messages.account_scheduling.payable_cancel'));

        return redirect()->route('payables.index');
    }
}
