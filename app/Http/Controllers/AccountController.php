<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use App\Services\AccountService;
use App\Http\Requests\TransferRequest;
use Illuminate\Database\QueryException;
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
            'accounts'  => $this->service->getAccounts(),

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
        $data = $request->validated();

        $account = $this->service->create($data);

        if (! $account) {
            Alert::error(__('global.invalid_request'), __('messages.not_save'));
            return redirect()->route('accounts.index');
        }

        Alert::success(__('global.success'), __('messages.accounts.create'));

        return redirect()->route('accounts.index');
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

        if (! $account) {
            Alert::error(__('global.invalid_request'), __('messages.accounts.not_found'));
            return redirect()->route('accounts.index');
        }

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
        $data       = $request->validated();
        $account    = $this->service->findById($id);

        if (! $account) {
            Alert::error(__('global.invalid_request'), __('messages.accounts.not_found'));
            return redirect()->route('accounts.index');
        }

        $this->service->update($account, $data);

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
        $account = $this->service->findById($id);

        if (! $account) {
            return response()
                    ->json(['title' => __('global.invalid_request'), 'text' => __('messages.accounts.not_found')], Response::HTTP_NOT_FOUND);
        }

        try {
            $account = $this->service->delete($account);
        } catch (QueryException $e) {
            return response()
                    ->json(['title' => __('global.invalid_request'), 'text' => __('messages.accounts.not_delete')], Response::HTTP_BAD_REQUEST);
        }

        return response()
                ->json(['title' => __('global.success'), 'text' => __('messages.accounts.delete')]);
    }

    public function transfer()
    {
        $data = [
            'title' => $this->title
        ];

        return view('accounts.transfer', $data);
    }

    public function transferStore(TransferRequest $request)
    {
        $data = $request->validated();

        try {
            $this->service->accountTransfer($data);
        } catch (\exception $e) {
            Alert::error(__('global.invalid'), $e->getMessage());
            return redirect()->back();
        }

        Alert::success(__('global.success'), __('messages.accounts.transfer_completed'));
        return redirect()->route('dashboard.index');
    }
}
