<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AccountService;
use App\Services\AccountEntryService;
use RealRashid\SweetAlert\Facades\Alert;
use App\Http\Requests\StoreAccountEntryRequest;

class AccountEntryController extends Controller
{
    public function __construct(AccountEntryService $service, AccountService $accountService)
    {
        $this->middleware(['auth', 'verified', 'accountEntryOwner']);

        $this->service          = $service;
        $this->accountService   = $accountService;

        $this->title = __('global.accounts_statement');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            'title'             => $this->title,
            'account_entries'   => $this->service->getEntriesForAccountStatement(),
        ];
        //dd($data);
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
        $data = $request->except('_token');

        $account = $this->accountService->findById($data['account_id']);

        $entry = $this->service->make($account, $data);

        //$this->cardService->updateCardBalance($card);
       
        Alert::success(__('global.success'), __('messages.entries.create'));
        return redirect()->route('account_entries.index');
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

        $entry = $this->service->update($id, $data);

        if (! $entry) {
            Alert::error(__('global.invalid_request'), __('messages.not_save'));
            return redirect()->route('invoices.index');
        }

        Alert::success(__('global.success'), __('messages.entries.update'));

        return redirect()->route('account_entries.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AccountEntry  $accountEntry
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! $this->service->delete($id)) {
            return response()
                    ->json(['title' => __('global.invalid_request'), 'text' => __('messages.not_delete')]);
        }

        return response()
                ->json(['title' => __('global.success'), 'text' => __('messages.entries.delete')]);
    }
}
