<?php

namespace App\Services;

use App\Models\AccountsScheduling;

class AccountsSchedulingService
{
    protected $account_scheduling;

    public function __construct(AccountsScheduling $account_scheduling)
    {
        $this->account_scheduling = $account_scheduling;
    }

    public function store(array $data)
    {
        return $this->account_scheduling->create($data);
    }

    public function update($id, array $data)
    {
        $account_scheduling = $this->findById($id);

        if (! $account_scheduling) {
            return false;
        }
        
        $account_scheduling->update($data);
        
        return $account_scheduling;
    }

    public function delete($id)
    {
        $account_scheduling = $this->findById($id);

        if (! $account_scheduling) {
            return false;
        }

        return $account_scheduling->delete();
    }

    public function findById($id)
    {
        return $this->account_scheduling->find($id);
    }

    /**
     * Get accounts payable/receivable by category type
     *
     * @param string $categoryType
     * @param array $range_date
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getCategoriesByType($categoryType, array $range_date = null)
    {
        $from = date('Y-m-1');
        $to = date('Y-m-t');

        if ($range_date && isset($range_date['from']) && isset($range_date['to'])) {
            $from = $range_date['from'];
            $to = $range_date['to'];
        }

        return $this->account_scheduling::select('accounts_schedulings.*')
            ->join('categories', 'categories.id', '=', 'accounts_schedulings.category_id')
            ->where('categories.type', $categoryType)
            ->where('date', '>=', $from)
            ->where('date', '<=', $to)
            ->orderBy('date')
            ->get();
    }
}