<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Services\AccountsSchedulingService;
use App\Http\Resources\AccountsSchedulingResource;

class ReportsController extends Controller
{
    protected $accountsSchedulingService;

    public function __construct(AccountsSchedulingService $accountsSchedulingService) {
        $this->accountsSchedulingService    = $accountsSchedulingService;
    }
    
    /**
     * Display a listing of the resource.
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
                'items' => AccountsSchedulingResource::collection($payables)->toArray($payables),
                'total' => $this->accountsSchedulingService->getItemsTotalAmount($payables)
            ];

            $receivables = $this->accountsSchedulingService->getAccountsSchedulingsByType(Category::INCOME, $filter);
            $data['receivables'] = [
                'items' => AccountsSchedulingResource::collection($receivables)->toArray($receivables),
                'total' => $this->accountsSchedulingService->getItemsTotalAmount($receivables)
            ];
        }

        return response()->json($data, Response::HTTP_OK);
    }
}
