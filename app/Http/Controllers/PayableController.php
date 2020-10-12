<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use App\Services\AccountsSchedulingService;

class PayableController extends Controller
{
    protected $service;

    public function __construct(AccountsSchedulingService $service)
    {
        $this->service = $service;
        $this->title = __('global.accounts_payable');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $range_date = null;

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
        //dd($range_date);
        $data = [
            'title'         => $this->title,
            'payables'       => $this->service->getCategoriesByType(Category::EXPENSE, $range_date)
        ];

        return view('payables.index', $data);
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
        ];

        return view('payables.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //$data = $request->validated();
        $data = $request->all();
        
        $account = $this->service->store($data);

        if (! $account) {
            Alert::error(__('global.invalid_request'), __('messages.not_save'));
            return redirect()->route('accounts-scheduling.index');
        }
   
        Alert::success(__('global.success'), __('messages.account_scheduling.payable_created'));

        return redirect()->route('payables.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $payable = $this->service->findById($id);

        if (! $payable) {
            Alert::error(__('global.invalid_request'), __('messages.not_found'));
            return redirect()->route('payables.index');
        }

        return view('payables.edit', [
            'title'     => $this->title,
            'payable'   => $payable 
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (! $this->service->update($id, $request->all())) {
            Alert::error(__('global.invalid_request'), __('messages.not_save'));

            return redirect()->route('payables.index');
        }

        Alert::success(__('global.success'), __('messages.account_scheduling.payable_updated'));

        return redirect()->route('payables.index');
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
                ->json(['title' => __('global.success'), 'text' => __('messages.account_scheduling.payable_deleted')]);
    }
}
