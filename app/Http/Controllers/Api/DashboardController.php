<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\InvoiceService;
use App\Services\DashboardService;
use App\Http\Controllers\Controller;
use App\Services\AccountEntryService;
use App\Services\InvoiceEntryService;

class DashboardController extends Controller
{
    protected $entriesService;
    protected $invoiceService;
    protected $dashboardService;
    protected $invoiceEntryService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(AccountEntryService $entriesService, 
        InvoiceService $invoiceService,
        InvoiceEntryService $invoiceEntryService,
        DashboardService $dashboardService
    ){
        $this->entriesService       = $entriesService;
        $this->invoiceService       = $invoiceService;
        $this->dashboardService     = $dashboardService;
        $this->invoiceEntryService  = $invoiceEntryService;
    }

    public function home() 
    {
        return redirect()->route('dashboard.index');
    }

    /**
     * Get values for dashboard
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard(Request $request)
    {
        $date = now()->format('Y-m-d');
        
        $validated = $request->validate([
            'date' => 'nullable|date_format:Y-m-d',
        ]);


        if (!empty($validated)) {
            $date = $validated['date'];
        }

        $data = [
            'months' => $this->dashboardService->getMonths($date),
            'total' => [
                'income'    => $this->entriesService->getTotalMonthlyByCategory(Category::INCOME, $date),
                'expense'   => $this->entriesService->getTotalMonthlyByCategory(Category::EXPENSE, $date),
                'invoices'  => $this->invoiceEntryService->getTotalMonthlyByCategory(Category::EXPENSE, $date),
            ],
            'pieChart' => [
                'income_category'         => $this->entriesService->getTotalByCategoryForChart(Category::INCOME, $date),
                'expense_category'        => $this->entriesService->getTotalByCategoryForChart(Category::EXPENSE, $date),
                'card_expense_category'   => $this->invoiceEntryService->getTotalByCategoryForChart($date),
            ],
            'barChart' => [
                'income'    => $this->entriesService->getTotalOfSixMonthsByCategoryTypeAndDate(Category::INCOME, $date),
                'expense'   => $this->entriesService->getTotalOfSixMonthsByCategoryTypeAndDate(Category::EXPENSE, $date),
            ],
            'lineChart' => [
                'invoices' => $this->invoiceService->getInvoiceAmountForSixMonthsForChart($date),
            ]
        ];

        return response()->json($data, Response::HTTP_OK);
    }
}
