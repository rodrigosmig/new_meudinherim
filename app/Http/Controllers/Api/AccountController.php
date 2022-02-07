<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\AccountService;
use App\Http\Controllers\Controller;
use App\Http\Resources\AccountResource;
use Illuminate\Database\QueryException;
use App\Http\Requests\Api\AccountUpdateStoreRequest;

class AccountController extends Controller
{
    public function __construct(AccountService $service)
    {
        $this->service = $service;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $accounts = $this->service->getAccounts();

        return AccountResource::collection($accounts);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AccountUpdateStoreRequest $request)
    {
        $data = $request->validated();

        $account = $this->service->create($data);

        return (new AccountResource($account))
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
        $account = $this->service->findById($id);

        if (! $account) {
            return response()->json(['message' => __('messages.accounts.api_not_found')], Response::HTTP_NOT_FOUND);
        }

        return new AccountResource($account);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AccountUpdateStoreRequest $request, $id)
    {
        $data = $request->validated();

        $account = $this->service->findById($id);

        if (! $account) {
            return response()->json(['message' => __('messages.accounts.api_not_found')], Response::HTTP_NOT_FOUND);
        }

        $this->service->update($account, $data);

        return (new AccountResource($account));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $account = $this->service->findById($id);

        if (! $account) {
            return response()->json(['message' => __('messages.accounts.api_not_found')], Response::HTTP_NOT_FOUND);
        }

        try {
            $account = $this->service->delete($account);
        } catch (QueryException $e) {
            return response()->json(['message' => __('messages.accounts.not_delete')], Response::HTTP_BAD_REQUEST);
        }

        return response()->json([], Response::HTTP_NO_CONTENT);
    }

    public function balance($id)
    {
        if ($id === 'all') {
            return $this->service->getAllAccountBalances();
        }

        $account = $this->service->findById($id);

        if (! $account) {
            return response()->json(['message' => __('messages.accounts.api_not_found')], Response::HTTP_NOT_FOUND);
        }

        return $this->service->getAccountBalance($account);
    }
}
