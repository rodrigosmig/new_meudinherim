<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\AccountEntryService;
use App\Services\InvoiceEntryService;
use App\Services\AccountsSchedulingService;

class ReportsController extends Controller
{
    protected $accountsSchedulingService;
    protected $accountEntryService;
    protected $invoiceEntryService;

    public function __construct(
        AccountsSchedulingService $accountsSchedulingService,
        AccountEntryService $accountEntryService,
        InvoiceEntryService $invoiceEntryService
    ){
        $this->accountsSchedulingService = $accountsSchedulingService;
        $this->accountEntryService = $accountEntryService;
        $this->invoiceEntryService = $invoiceEntryService;

        $this->title = __('global.reports');
    }

    public function payables(Request $request)
    {
        $filter = null;
        
        $data = [
            'title' => $this->title . " - " . __('global.accounts_payable')
        ];

        if (isset($request->filter_from)
            && $request->filter_from
            && isset($request->filter_to)
            && $request->filter_to
            && $request->filter_status
        ) {
            $filter = [
                'from'      => $request->filter_from,
                'to'        => $request->filter_to,
                'status'    => $request->filter_status
            ];

            $data['payables'] = $this->accountsSchedulingService->getAccountsSchedulingsByType(Category::EXPENSE, $filter);
            $data['total'] = $this->accountsSchedulingService->getTotalForReportByCategoryType($data['payables']);
            $data['from'] = $filter['from'];
            $data['to'] = $filter['to'];
        }

        return view('reports.payables', $data);
    }

    public function receivables(Request $request)
    {
        $filter = null;
        
        $data = [
            'title' => $this->title . " - " . __('global.accounts_receivable')
        ];

        if (isset($request->filter_from)
            && $request->filter_from
            && isset($request->filter_to)
            && $request->filter_to
            && $request->filter_status
        ) {
            $filter = [
                'from'      => $request->filter_from,
                'to'        => $request->filter_to,
                'status'    => $request->filter_status
            ];

            $data['receivables'] = $this->accountsSchedulingService->getAccountsSchedulingsByType(Category::INCOME, $filter);
            $data['total'] = $this->accountsSchedulingService->getTotalForReportByCategoryType($data['receivables']);
            $data['from'] = $filter['from'];
            $data['to'] = $filter['to'];
        }

        return view('reports.receivables', $data);
    }

    public function totalByCategory(Request $request)
    {
        $filter = null;
        
        $data = [
            'title' => $this->title
        ];

        if (isset($request->filter_from)
            && $request->filter_from
            && isset($request->filter_to)
            && $request->filter_to
        ) {
            $filter = [
                'from'  => $request->filter_from,
                'to'    => $request->filter_to,
            ];

            $data['incomes']    = $this->accountEntryService->getTotalByCategoryTypeForRangeDate(Category::INCOME, $filter);
            $data['expenses']   = $this->accountEntryService->getTotalByCategoryTypeForRangeDate(Category::EXPENSE, $filter);
            $data['cards']      = $this->invoiceEntryService->getTotalByCategoryTypeForRangeDate(Category::EXPENSE, $filter);
            $data['from'] = $filter['from'];
            $data['to'] = $filter['to'];
        }

        return view('reports.total_by_category', $data);
    }

    public function ajaxtotalByCategory(Request $request)
    {
        $category_id = $request->query('category_id', '');
        $from = $request->query('from', '');
        $to = $request->query('to', '');
        $type = $request->query('type', '');
        $entries = [];

        if (! $category_id || ! $from || ! $to || ! $type) {
            return response()->json(['status' => 'error', 'message' => __('messages.entries.not_found')], Response::HTTP_BAD_REQUEST);
        }

        if ($type === 'account') {
            $entries = $this->accountEntryService->getEntriesByCategoryAndRangeDate($from, $to, $category_id);
        } elseif ($type === 'card') {
            $entries = $this->invoiceEntryService->getEntriesByCategoryAndRangeDate($from, $to, $category_id);
        }

        return response()
            ->json($entries);
    }
}
