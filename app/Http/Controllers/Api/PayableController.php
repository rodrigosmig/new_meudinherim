<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\AccountService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PaymentRequest;
use App\Services\AccountsSchedulingService;
use App\Http\Requests\Api\PayableStoreRequest;
use App\Http\Requests\Api\PayableUpdateRequest;
use App\Http\Resources\AccountsSchedulingResource;
class PayableController extends Controller
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
        $per_page = isset($request->per_page) && is_numeric(($request->per_page)) ? $request->per_page : 10;
        $page = isset($request->page) && is_numeric($request->page) ? $request->page : 1;

        if ((isset($request->from)
            && $request->from
            && isset($request->to)
            && $request->to)
        ) {
            $filter = [
                'from'      => $request->from,
                'to'        => $request->to,
            ];
        }

        if (isset($request->status) && $request->status) {
            $filter['status'] = $request->status;
        }

        $payables = $this->service->getAccountsSchedulingsByType(Category::EXPENSE, $filter);

        $payables_collection = AccountsSchedulingResource::collection($payables)->toArray($payables);

        $results = $this->service->paginate($page, $per_page, $payables_collection);

        return response()->json($results);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PayableStoreRequest $request)
    {
        $data = $request->validated();

        $payable = $this->service->create($data);

        if (is_array($payable)) {
            return AccountsSchedulingResource::collection($payable);
        }

        return (new AccountsSchedulingResource($payable))
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
        $parcelable_id = request()->get('parcelable_id', false);

        if($parcelable_id) {
            $payable = $this->service->findParcel($id, $parcelable_id);
        } else {
            $payable = $this->service->findById($id);
        }

        if (! $payable) {
            return response()->json(['message' => __('messages.account_scheduling.api_not_found')], Response::HTTP_NOT_FOUND);
        }

        if (! $payable->isExpenseCategory()) {
            return response()->json(['message' => __('messages.account_scheduling.api_not_found')], Response::HTTP_NOT_FOUND);
        }

        return new AccountsSchedulingResource($payable);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PayableUpdateRequest $request, $id)
    {
        $data       = $request->validated();
        $payable    = $this->service->findById($id);
        
        if (! $payable) {
            return response()->json(['message' => __('messages.account_scheduling.api_not_found')], Response::HTTP_NOT_FOUND);
        }

        if ($payable->isPaid()) {
            return response()->json(['message' => __('messages.account_scheduling.update_payable_paid')], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->service->update($payable, $data);

        return (new AccountsSchedulingResource($payable));
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
            return response()->json(['message' => __('messages.account_scheduling.api_not_found')], Response::HTTP_NOT_FOUND);
        }

        if ($payable->isPaid()) {
            return response()->json(['message' => __('messages.account_scheduling.delete_payable_paid')], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (! $payable->isExpenseCategory()) {
            return response()->json(['message' => __('messages.account_scheduling.not_payable')], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->service->delete($payable);

        return response()->json([], Response::HTTP_NO_CONTENT);
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
            return response()->json(['message' => __('messages.account_scheduling.api_not_found')], Response::HTTP_NOT_FOUND);
        }

        if ($payable->isPaid()) {
            return response()->json(['message' => __('messages.account_scheduling.delete_payable_paid')], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $account = $this->accountService->findById($data['account_id']);

        $this->service->payment($account, $payable, $data);

        return response()->json(['message' => __('messages.account_scheduling.payable_paid')], Response::HTTP_OK);
    }

    public function cancelPayment($id) {
        $parcelable_id = request()->get('parcelable_id', false);

        if($parcelable_id) {
            $payable = $this->service->findParcel($id, $parcelable_id);
        } else {
            $payable = $this->service->findById($id);
        }

        if (! $payable) {
            return response()->json(['message' => __('messages.account_scheduling.api_not_found')], Response::HTTP_NOT_FOUND);
        }

        if (! $payable->isPaid()) {
            return response()->json(['message' => __('messages.account_scheduling.account_is_not_paid')], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->service->cancelPayment($payable);

        return response()->json(['message' => __('messages.account_scheduling.payable_cancel')], Response::HTTP_OK);
    }
}
