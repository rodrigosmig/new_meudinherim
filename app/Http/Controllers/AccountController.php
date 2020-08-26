<?php

namespace App\Http\Controllers;

use App\Services\AccountService;
use RealRashid\SweetAlert\Facades\Alert;
use App\Http\Requests\StoreUpdateAccountRequest;

class AccountController extends Controller
{
    /* The AccountService instance.
	 *
	 * @var AccountService
	 */
    private $service;

    public function __construct(AccountService $service)
    {
        $this->middleware(['auth', 'verified', 'accountOwner']);

        $this->service = $service;
        $this->title = __('global.accounts');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            'title'     => $this->title,
            'accounts'  => $this->service->getAccountsByUser(),

        ];

        return view('accounts.index', $data);
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
            'types' => $this->service->getTypeList()

        ];

        return view('accounts.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUpdateAccountRequest $request)
    {
        $this->service->store($request->all());

        Alert::success(__('global.success'), __('messages.accounts.create'));

        return redirect()->route('accounts.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $account = $this->service->findById($id);

        return view('accounts.edit', [
            'account'  => $account,
            'types' => $this->service->getTypeList(),
            'title'     => $this->title
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreUpdateAccountRequest $request, $id)
    {
        if (! $this->service->update($id, $request->all())) {
            Alert::error(__('global.invalid_request'), __('messages.not_save'));

            return redirect()->route('accounts.index');
        }

        Alert::success(__('global.success'), __('messages.accounts.update'));

        return redirect()->route('accounts.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! $this->service->delete($id)) {
            return response()
                    ->json(['title' => __('global.invalid_request'), 'text' => __('messages.not_delete')]);
        }

        return response()
                ->json(['title' => __('global.success'), 'text' => __('messages.accounts.delete')]);
    }
}
