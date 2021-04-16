<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\AccountService;
use App\Http\Controllers\Controller;
use App\Services\AccountEntryService;
use App\Http\Resources\AccountEntryResource;
use App\Http\Requests\Api\StoreUpdateAccountEntryRequest;

class AccountEntryController extends Controller
{
    protected $entryService;
    protected $accountService;

    public function __construct(AccountEntryService $entryService, AccountService $accountService)
    {
        $this->entryService     = $entryService;
        $this->accountService   = $accountService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $account_id)
    {
        $account = $this->accountService->findById($account_id);

        if (! $account) {
            return response()->json(['message' => __('messages.accounts.api_not_found')], Response::HTTP_NOT_FOUND);
        }

        $range_date = [];

        if (isset($request->from)
            && $request->from
            && isset($request->to)
            && $request->to
        ) {
            $range_date = [
                'from'  => $request->from,
                'to'    => $request->to
            ];
        }

        $entries = $this->entryService->getEntriesByAccountId($account->id, $range_date);

        return AccountEntryResource::collection($entries);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUpdateAccountEntryRequest $request, $account_id)
    {
        $data = $request->validated();

        $account = $this->accountService->findById($account_id);

        if (! $account) {
            return response()->json(['message' => __('messages.accounts.api_not_found')], Response::HTTP_NOT_FOUND);
        }

        $entry = $this->entryService->create($account_id, $data);

        $this->accountService->updateBalance($account, $entry->date);

        return new AccountEntryResource($entry);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $entry = $this->entryService->findById($id);

        if (! $entry) {
            return response()->json(['message' => __('messages.entries.api_not_found')], Response::HTTP_NOT_FOUND);
        }

        return new AccountEntryResource($entry);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreUpdateAccountEntryRequest $request, $id)
    {
        $data = $request->validated();

        $entry = $this->entryService->findById($id);

        if (! $entry) {
            return response()->json(['message' => __('messages.entries.api_not_found')], Response::HTTP_NOT_FOUND);
        }

        $this->entryService->update($entry, $data);

        $this->accountService->updateBalance($entry->account, $entry->date);

        return new AccountEntryResource($entry);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $entry = $this->entryService->findById($id);

        if (! $entry) {
            return response()->json(['message' => __('messages.entries.api_not_found')], Response::HTTP_NOT_FOUND);
        }

        $date       = $entry->date;
        $account    = $entry->account;

        $this->entryService->delete($entry);

        $this->accountService->updateBalance($account, $date);

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
