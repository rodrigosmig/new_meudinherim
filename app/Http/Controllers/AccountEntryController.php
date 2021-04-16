<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AccountService;
use App\Services\AccountEntryService;
use RealRashid\SweetAlert\Facades\Alert;
use App\Http\Requests\StoreAccountEntryRequest;

class AccountEntryController extends Controller
{
    protected $service;
    protected $accountService;

    public function __construct(AccountEntryService $service, AccountService $accountService)
    {
        $this->service          = $service;
        $this->accountService   = $accountService;

        $this->title = __('global.extract');
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
            Alert::error(__('global.invalid_request'), __('messages.accounts.not_found'));
            return redirect()->route('accounts.index');
        }

        $range_date = [];

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

        $data = [
            'title'     => $this->title,
            'account'   => $account,
            'entries'   => $this->service->getEntriesByAccountId($account->id, $range_date),
            'filter'    => $range_date
        ];

        return view('account_entries.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [
            'title' => __('global.add_entry'),
        ];

        return view('account_entries.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAccountEntryRequest $request)
    {
        $data = $request->validated();

        $account = $this->accountService->findById($data['account_id']);

        if (! $account) {
            Alert::error(__('global.invalid_request'), __('messages.accounts.not_found'));
            return redirect()->route('accounts.index');
        }

        $entry = $this->service->create($account->id, $data);

        $this->accountService->updateBalance($account, $entry->date);
       
        Alert::success(__('global.success'), __('messages.entries.create'));
        return redirect()->route('accounts.entries', $entry->account->id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\AccountEntry  $accountEntry
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $entry = $this->service->findById($id);

        if (! $entry) {
            Alert::error(__('global.invalid_request'), __('messages.entries.not_found'));
            return redirect()->route('accounts.index');
        }

        return view('account_entries.edit', [
            'title' => __('global.account_entry'),
            'entry' => $entry
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\AccountEntry  $accountEntry
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->except('_token');

        $entry = $this->service->findById($id);

        if (! $entry) {
            Alert::error(__('global.invalid_request'), __('messages.entries.not_found'));
            return redirect()->route('accounts.index');
        }

        $this->service->update($entry, $data);

        $this->accountService->updateBalance($entry->account, $entry->date);

        Alert::success(__('global.success'), __('messages.entries.update'));

        return redirect()->route('accounts.entries', $entry->account->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AccountEntry  $accountEntry
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $entry = $this->service->findById($id);

        if (! $entry) {
            return response()
                ->json(['title' => __('global.invalid_request'), 'text' => __('messages.entries.not_found')]);
        }

        $date       = $entry->date;
        $account    = $entry->account;

        $this->service->delete($entry);

        $this->accountService->updateBalance($account, $date);

        return response()
                ->json(['title' => __('global.success'), 'text' => __('messages.entries.delete')]);
    }
}
