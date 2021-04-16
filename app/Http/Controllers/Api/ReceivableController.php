<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\AccountService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PaymentRequest;
use App\Exceptions\AccountIsPaidException;
use App\Services\AccountsSchedulingService;
use App\Exceptions\AccountIsNotPaidException;
use App\Http\Requests\Api\ReceivableStoreRequest;
use App\Http\Requests\Api\ReceivableUpdateRequest;
use App\Http\Resources\AccountsSchedulingResource;

class ReceivableController extends Controller
{
    protected $service;
    protected $accountService;

    public function __construct(AccountsSchedulingService $service, AccountService $accountService)
    {
        $this->service = $service;
        $this->accountService = $accountService;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filter = [];

        if ((isset($request->from)
            && $request->from
            && isset($request->to)
            && $request->to)
        ) {
            $filter = [
                'from'      => $request->from,
                'to'        => $request->to,
                'status'    => $request->status
            ];
        }

        if (isset($request->filter_status) && $request->filter_status) {
            $filter['status'] = $request->filter_status;
        }

        $payables = $this->service->getAccountsSchedulingsByType(Category::INCOME, $filter);

        return AccountsSchedulingResource::collection($payables);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ReceivableStoreRequest $request)
    {
        $data = $request->validated();

        $receivable = $this->service->create($data);

        if (gettype($receivable) === 'boolean') {
            return AccountsSchedulingResource::collection($receivable);
            //return response()->json(['message' => __('messages.account_scheduling.installments_created')]);
        }

        return (new AccountsSchedulingResource($receivable))
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
        $receivable = $this->service->findById($id);

        if (! $receivable) {
            return response()->json(['message' => __('messages.account_scheduling.api_not_found')], Response::HTTP_NOT_FOUND);
        }

        if ($receivable->isExpenseCategory()) {
            return response()->json(['message' => __('messages.account_scheduling.api_not_found')], Response::HTTP_NOT_FOUND);
        }

        return new AccountsSchedulingResource($receivable);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ReceivableUpdateRequest $request, $id)
    {
        $data       = $request->validated();
        $receivable = $this->service->findById($id);

        if (! $receivable) {
            return response()->json(['message' => __('messages.account_scheduling.api_not_found')], Response::HTTP_NOT_FOUND);
        }

        if ($receivable->isPaid()) {
            return response()->json(['message' => __('messages.account_scheduling.delete_receivable_paid')], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->service->update($receivable, $data);

        return (new AccountsSchedulingResource($receivable));
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
            return response()->json(['message' => __('messages.account_scheduling.api_not_found')], Response::HTTP_NOT_FOUND);
        }

        if ($receivable->isPaid()) {
            return response()->json(['message' => __('messages.account_scheduling.delete_receivable_paid')], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($receivable->isExpenseCategory()) {
            return response()->json(['message' => __('messages.account_scheduling.not_receivable')], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->service->delete($receivable);

        return response()->json([], Response::HTTP_NO_CONTENT);
    }

    public function payment(PaymentRequest $request, $id)
    {
        $data       = $request->validated();
        $receivable = $this->service->findById($id);

        if (! $receivable) {
            return response()->json(['message' => __('messages.account_scheduling.api_not_found')], Response::HTTP_NOT_FOUND);
        }

        if ($receivable->isPaid()) {
            return response()->json(['message' => __('messages.account_scheduling.delete_receivable_paid')], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $account = $this->accountService->findById($data['account_id']);

        $this->service->payment($account, $receivable, $data);

        return response()->json(['message' => __('messages.account_scheduling.receivable_paid')], Response::HTTP_OK);
    }

    public function cancelPayment($id) {
        $receivable = $this->service->findById($id);

        if (! $receivable) {
            return response()->json(['message' => __('messages.account_scheduling.api_not_found')], Response::HTTP_NOT_FOUND);
        }

        if (! $receivable->isPaid()) {
            return response()->json(['message' => __('messages.account_scheduling.delete_receivable_paid')], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response()->json(['message' => __('messages.account_scheduling.receivable_cancel')], Response::HTTP_OK);
    }
}
