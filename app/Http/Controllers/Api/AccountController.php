<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\AccountService;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use App\Http\Requests\Api\AccountUpdateStoreRequest;
use App\Http\Resources\AccountResource;

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

        $account = $this->service->store($data);

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
        $account = $this->service->update($id, $request->validated());

        if (! $account) {
            return response()->json(['message' => __('messages.accounts.api_not_found')], Response::HTTP_NOT_FOUND);
        }

        return (new AccountResource($this->service->findById($id)));
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
            $account = $this->service->delete($id);
        } catch (QueryException $e) {
            return response()->json(['message' => __('messages.accounts.not_delete')], Response::HTTP_BAD_REQUEST);
        }

        if (! $account) {
            return response()->json(['message' => __('messages.accounts.api_not_found')], Response::HTTP_NOT_FOUND);
        }

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
