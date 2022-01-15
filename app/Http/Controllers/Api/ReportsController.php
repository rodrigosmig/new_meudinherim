<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\InvoiceService;
use App\Services\CategoryService;
use App\Http\Controllers\Controller;
use App\Services\AccountEntryService;
use App\Services\InvoiceEntryService;
use App\Http\Resources\CardReportResource;
use App\Services\AccountsSchedulingService;
use App\Http\Resources\AccountsReportResource;
use App\Http\Resources\AccountsSchedulingResource;

class ReportsController extends Controller
{
    protected $accountsSchedulingService;
    protected $accountEntryService;
    protected $invoiceEntryService;
    protected $categoryService;
    protected $invoiceService;

    public function __construct(
        AccountsSchedulingService $accountsSchedulingService,
        AccountEntryService $accountEntryService,
        InvoiceEntryService $invoiceEntryService,
        CategoryService $categoryService,
        InvoiceService $invoiceService
    ) {
        $this->accountsSchedulingService    = $accountsSchedulingService;
        $this->accountEntryService          = $accountEntryService;
        $this->invoiceEntryService          = $invoiceEntryService;
        $this->categoryService              = $categoryService;
        $this->invoiceService               = $invoiceService;
    }
    
    /**
     *
     * @return \Illuminate\Http\Response
     */
    public function accounts(Request $request)
    {
        $filter = [];
        $data = [];

        if (isset($request->from)
            && $request->from
            && isset($request->to)
            && $request->to
            && $request->status
        ) {
            $filter = [
                'from'      => $request->from,
                'to'        => $request->to,
                'status'    => $request->status
            ];
            
            $payables = $this->accountsSchedulingService->getAccountsSchedulingsByType(Category::EXPENSE, $filter);
            
            $data['payables'] = [
                'items' => AccountsSchedulingResource::collection($payables),
                'total' => $this->accountsSchedulingService->getItemsTotalAmount($payables)
            ];

            $receivables = $this->accountsSchedulingService->getAccountsSchedulingsByType(Category::INCOME, $filter);
            $data['receivables'] = [
                'items' => AccountsSchedulingResource::collection($receivables),
                'total' => $this->accountsSchedulingService->getItemsTotalAmount($receivables)
            ];

            $data['invoices'] = [
                'total' => $this->invoiceService->getTotalOfOpenInvoices(['from' => $filter['from'], 'to' => $filter['to']])
            ];
        }

        return response()->json($data, Response::HTTP_OK);
    }

    /**
     *
     * @return \Illuminate\Http\Response
     */
    public function getTotalByCategory(Request $request)
    {
        $filter = [];
        $data = [];

        if (isset($request->from)
            && $request->from
            && isset($request->to)
            && $request->to
        ) {
            $filter = [
                'from'      => $request->from,
                'to'        => $request->to,
            ];

            $data['incomes']    = $this->accountEntryService->getTotalByCategoryTypeForRangeDate(Category::INCOME, $filter);
            $data['expenses']   = $this->accountEntryService->getTotalByCategoryTypeForRangeDate(Category::EXPENSE, $filter);            
            $data['creditCard'] = $this->categoryService->getTotalOfInvoiceEntriesByCategoryTypeForApi(Category::EXPENSE, $filter);
        }

        return response()->json($data, Response::HTTP_OK);
    }

    /**
     *
     * @return \Illuminate\Http\Response
     */
    public function getTotalByCategoryDetailed(Request $request)
    {
        $category_id = $request->query('category_id', '');
        $from = $request->query('from', '');
        $to = $request->query('to', '');
        $type = $request->query('type', '');

        $entries = [];

        if (!$category_id || !$from || !$to || !$type) {
            return response()->json(['status' => 'error', 'message' => __('messages.entries.not_found')], Response::HTTP_BAD_REQUEST);
        }

        if ($type === 'account') {
            $entries = $this->accountEntryService->getEntriesByCategoryAndRangeDate($from, $to, $category_id);
            return AccountsReportResource::collection($entries);
        } 
        
        if ($type === 'card') {
            $entries = $this->invoiceEntryService->getEntriesByCategoryAndRangeDate($from, $to, $category_id);
            return CardReportResource::collection($entries);
        }

        return response()->json($entries, Response::HTTP_OK);
    }
}
