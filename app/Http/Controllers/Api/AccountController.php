<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\AccountService;
use App\Http\Controllers\Controller;
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
        return response()->json($this->service->getAccounts());
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

        return response()->json($account, Response::HTTP_CREATED);
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

        return response()->json($account);
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

        return response()->json($this->service->findById($id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $account = $this->service->delete($id);

        if (! $account) {
            return response()->json(['message' => __('messages.accounts.api_not_found')], Response::HTTP_NOT_FOUND);
        }

        return response()->json();
    }
}
