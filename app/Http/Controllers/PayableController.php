<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Services\AccountService;
use App\Http\Requests\PaymentRequest;
use RealRashid\SweetAlert\Facades\Alert;
use App\Http\Requests\StorePayableRequest;
use App\Services\AccountsSchedulingService;

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
        $filter = [];

        if (isset($request->filter_from)
            && $request->filter_from
            && isset($request->filter_to)
            && $request->filter_to
        ) {
            $filter = [
                'from'      => $request->filter_from,
                'to'        => $request->filter_to,
            ];
        }

        if (isset($request->filter_status) && $request->filter_status) {
            $filter['status'] = $request->filter_status;
        }

        $data = [
            'title'         => $this->title,
            'payables'      => $this->service->getAccountsSchedulingsByType(Category::EXPENSE, $filter),
            'filter'        => $filter
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
        
        $payable = $this->service->create($data);

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
        $parcelable_id = request()->get('parcelable_id', false);

        if($parcelable_id) {
            $payable = $this->service->findParcel($id, $parcelable_id);
        } else {
            $payable = $this->service->findById($id);
        }

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
        $data       = $request->validated();
        $payable = $this->service->findById($id);

        if (! $payable) {
            Alert::error(__('global.invalid_request'), __('messages.account_scheduling.not_found'));
            return redirect()->route('payables.index');
        }

        if ($payable->isPaid()) {
            Alert::error(__('global.invalid_request'), __('messages.account_scheduling.payable_is_paid'));
            return redirect()->route('payables.index');
        }

        $this->service->update($payable, $data);

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
        $payable = $this->service->findById($id);

        if (! $payable) {
            return response()
                ->json(['title' => __('global.invalid_request'), 'text' => __('messages.account_scheduling.not_found')]);
        }

        if ($payable->isPaid()) {
            return response()
                ->json(['title' => __('global.invalid_request'), 'text' => __('messages.account_scheduling.payable_is_paid')]);
        }

        if (! $payable->isExpenseCategory()) {
            return response()
                ->json(['title' => __('global.invalid_request'), 'text' => __('messages.account_scheduling.not_payable')]);
        }

        $this->service->delete($payable);

        return response()
                ->json(['title' => __('global.success'), 'text' => __('messages.account_scheduling.payable_deleted')]);
    }

    public function payment(PaymentRequest $request, $id)
    {
        $data = $request->validated();

        if(isset($data['parcelable_id']) && $data['parcelable_id']) {
            $payable = $this->service->findParcel($id, $data['parcelable_id']);
        } else {
            $payable = $this->service->findById($id);
        }

        if (! $payable) {
            Alert::error(__('global.invalid_request'), __('messages.account_scheduling.not_found'));
            return redirect()->route('payables.index');
        }

        if ($payable->isPaid()) {
            Alert::error(__('global.invalid_request'), __('messages.account_scheduling.account_is_paid'));
            return redirect()->route('payables.index');
        }

        $account = $this->accountService->findById($data['account_id']);

        $this->service->payment($account, $payable, $data);

        Alert::success(__('global.success'), __('messages.account_scheduling.payable_paid'));

        return redirect()->route('payables.index');
    }

    public function cancelPayment($id) {
        $parcelable_id = request()->get('parcelable_id', false);

        if($parcelable_id) {
            $payable = $this->service->findParcel($id, $parcelable_id);
        } else {
            $payable = $this->service->findById($id);
        }

        if (! $payable) {
            Alert::error(__('global.invalid_request'), __('messages.not_found'));
            return redirect()->route('payables.index');
        }

        if (! $payable->isPaid()) {
            Alert::error(__('global.invalid_request'), __('messages.account_scheduling.account_is_not_paid'));
            return redirect()->route('payables.index');
        }

        $this->service->cancelPayment($payable);

        Alert::success(__('global.success'), __('messages.account_scheduling.payable_cancel'));

        return redirect()->route('payables.index');
    }
}
