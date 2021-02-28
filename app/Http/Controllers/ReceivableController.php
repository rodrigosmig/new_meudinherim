<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Services\AccountService;
use RealRashid\SweetAlert\Facades\Alert;
use App\Http\Requests\ReceivementRequest;
use App\Exceptions\AccountIsPaidException;
use App\Services\AccountsSchedulingService;
use App\Exceptions\AccountIsNotPaidException;
use App\Http\Requests\StoreReceivableRequest;

class ReceivableController extends Controller
{
    protected $service;
    protected $accountService;

    public function __construct(AccountsSchedulingService $service, AccountService $accountService)
    {
        $this->service = $service;
        $this->accountService = $accountService;
        $this->title = __('global.accounts_receivable');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filter = null;

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
            'receivables'   => $this->service->getAccountsSchedulingsByType(Category::INCOME, $filter),
            'filter'        => $filter
        ];

        return view('receivables.index', $data);
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

        return view('receivables.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreReceivableRequest $request)
    {
        $data = $request->validated();
        
        $receivable = $this->service->store($data);

        if (! $receivable) {
            Alert::error(__('global.invalid_request'), __('messages.not_save'));
            return redirect()->route('accounts-scheduling.index');
        }
   
        Alert::success(__('global.success'), __('messages.account_scheduling.receivable_created'));

        return redirect()->route('receivables.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $receivable = $this->service->findById($id);

        if (! $receivable) {
            Alert::error(__('global.invalid_request'), __('messages.not_found'));
            return redirect()->route('receivables.index');
        }

        return view('receivables.show', [
            'title'         => $this->title,
            'receivable'    => $receivable
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
        $receivable = $this->service->findById($id);

        if (! $receivable) {
            Alert::error(__('global.invalid_request'), __('messages.not_found'));
            return redirect()->route('receivables.index');
        }

        if ($receivable->isPaid()) {
            return redirect()->route('receivables.show', $receivable->id);
        }

        return view('receivables.edit', [
            'title'     => $this->title,
            'receivable'   => $receivable 
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreReceivableRequest $request, $id)
    {
        $data       = $request->validated();
        $receivable = $this->service->findById($id);

        if (! $receivable) {
            Alert::error(__('global.invalid_request'), __('messages.account_scheduling.not_found'));
            return redirect()->route('receivables.index');
        }

        if ($receivable->isPaid()) {
            Alert::error(__('global.invalid_request'), __('messages.account_scheduling.receivable_is_paid'));
            return redirect()->route('receivables.index');
        }

        $this->service->update($receivable, $data);

        Alert::success(__('global.success'), __('messages.account_scheduling.receivable_updated'));

        return redirect()->route('receivables.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $receivable = $this->service->findById($id);

        if (! $receivable) {
            return response()
                ->json(['title' => __('global.invalid_request'), 'text' => __('messages.account_scheduling.not_found')]);
        }

        if ($receivable->isPaid()) {
            return response()
                ->json(['title' => __('global.invalid_request'), 'text' => __('messages.account_scheduling.receivable_is_paid')]);
        }

        if ($receivable->isExpenseCategory()) {
            return response()
                ->json(['title' => __('global.invalid_request'), 'text' => __('messages.account_scheduling.not_receivable')]);
        }

        $this->service->delete($receivable);
        
        return response()
                ->json(['title' => __('global.success'), 'text' => __('messages.account_scheduling.receivable_deleted')]);
    }

    public function receivement(ReceivementRequest $request, $id)
    {
        $data       = $request->validated();
        $data['id'] = $id;
        $account    = $this->accountService->findById($data['account_id']);

        try {
            $entry = $this->service->payment($account, $data);
        } catch (AccountIsPaidException $exception) {
            Alert::error(__('global.invalid_request'), $exception->getMessage());
            return redirect()->route('receivables.index');
        }

        if (! $entry) {
            Alert::error(__('global.invalid_request'), __('messages.not_found'));
            return redirect()->route('receivables.index');
        }

        $this->accountService->updateBalance($account, $data['paid_date']);

        Alert::success(__('global.success'), __('messages.account_scheduling.receivable_paid'));

        return redirect()->route('receivables.index');
    }

    public function cancelreceivement($id) {
        $receivable = $this->service->findById($id);

        if (! $receivable) {
            Alert::error(__('global.invalid_request'), __('messages.not_found'));
            return redirect()->route('receivables.index');
        }

        try {
            $response = $this->service->cancelPayment($receivable);
        } catch (AccountIsNotPaidException $exception) {
            Alert::error(__('global.invalid_request'), $exception->getMessage());
            return redirect()->route('receivables.index');
        }

        if (! $response) {
            Alert::error(__('global.invalid_request'), __('messages.account_scheduling.not_cancel_receivement'));
            return redirect()->route('receivables.index');
        }

        Alert::success(__('global.success'), __('messages.account_scheduling.receivable_cancel'));

        return redirect()->route('receivables.index');
    }
}
