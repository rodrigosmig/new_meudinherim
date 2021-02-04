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
use App\Http\Requests\Api\ReceivableUpdateStoreRequest;

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
        $filter = null;

        if ((isset($request->from)
            && $request->from
            && isset($request->to)
            && $request->to)
            || $request->status
        ) {
            $filter = [
                'from'      => $request->from,
                'to'        => $request->to,
                'status'    => $request->status
            ];
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

        $receivable = $this->service->store($data);

        if (gettype($receivable) === 'boolean') {
            return response()->json(['message' => __('messages.account_scheduling.installments_created')]);
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
        try {
            $receivable = $this->service->update($id, $request->validated());
        } catch (AccountIsPaidException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (! $receivable) {
            return response()->json(['message' => __('messages.categories.api_not_found')], Response::HTTP_NOT_FOUND);
        }

        return (new AccountsSchedulingResource($this->service->findById($id)));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $receivable = $this->service->delete($id);
        } catch (AccountIsPaidException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (! $receivable) {
            return response()->json(['message' => __('messages.account_scheduling.api_not_found')], Response::HTTP_NOT_FOUND);
        }

        return response()->json([], Response::HTTP_NO_CONTENT);
    }

    public function payment(PaymentRequest $request, $id)
    {
        $data       = $request->validated();
        $data['id'] = $id;
        $account    = $this->accountService->findById($data['account_id']);

        try {
            $entry = $this->service->payment($account, $data);
        } catch (AccountIsPaidException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (! $entry) {
            return response()->json(['message' => __('messages.account_scheduling.api_not_found')], Response::HTTP_NOT_FOUND);
        }

        $this->accountService->updateBalance($account, $data['paid_date']);

        return response()->json(['message' => __('messages.account_scheduling.receivable_paid')], Response::HTTP_OK);
    }

    public function cancelPayment($id) {
        $receivable = $this->service->findById($id);

        if (! $receivable) {
            return response()->json(['message' => __('messages.account_scheduling.api_not_found')], Response::HTTP_NOT_FOUND);
        }

        try {
            $this->service->cancelPayment($receivable);
        } catch (AccountIsNotPaidException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response()->json(['message' => __('messages.account_scheduling.receivable_cancel')], Response::HTTP_OK);
    }
}
