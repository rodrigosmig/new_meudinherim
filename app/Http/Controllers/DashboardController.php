<?php

namespace App\Http\Controllers;

use DateTime;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Services\InvoiceService;
use App\Services\DashboardService;
use App\Services\AccountEntryService;
use App\Http\Requests\DashboardFormRequest;
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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(DashboardFormRequest $request)
    {
        $date = $this->dashboardService->getSessionDate($request->validated());

        $data = [
            'title'                         => $this->dashboardService->dateToString($date),
            'months'                        => $this->dashboardService->getMonths($date),
            'total_income'                  => $this->entriesService->getTotalMonthlyByCategory(Category::INCOME, $date),
            'total_expense'                 => $this->entriesService->getTotalMonthlyByCategory(Category::EXPENSE, $date),
            'total_invoices'                => $this->invoiceService->getTotalMonthlyByCategory($date),
            'total_income_in_six_months'    => $this->entriesService->getTotalOfSixMonthsByCategoryTypeAndDate(Category::INCOME, $date),
            'total_expense_in_six_months'   => $this->entriesService->getTotalOfSixMonthsByCategoryTypeAndDate(Category::EXPENSE, $date),
            'total_invoices_in_six_monthss' => $this->invoiceService->getTotalInvoicesForSixMonthsForChart($date),
            'total_income_category'         => $this->entriesService->getTotalByCategoryForChart(Category::INCOME, $date),
            'total_expense_category'        => $this->entriesService->getTotalByCategoryForChart(Category::EXPENSE, $date),
            'total_card_expense_category'   => $this->invoiceEntryService->getTotalByCategoryForChart($date),
            
        ];

        return view('home', $data);
    }
}
