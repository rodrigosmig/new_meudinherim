<?php

namespace App\Repositories\Core\Eloquent;

use App\Models\AccountsScheduling;
use App\Repositories\Core\BaseEloquentRepository;
use App\Repositories\Interfaces\AccountsSchedulingRepositoryInterface;


class AccountsSchedulingRepository extends BaseEloquentRepository implements AccountsSchedulingRepositoryInterface
{
    protected $model = AccountsScheduling::class;

    /**
     * Get accounts payable/receivable by category type
     *
     * @param string $categoryType
     * @param array $filter
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getAccountsSchedulingsByType($categoryType, array $filter = null)
    {
        $result = $this->model::select('accounts_schedulings.*')
            ->join('categories', 'categories.id', '=', 'accounts_schedulings.category_id')
            ->where('categories.type', $categoryType)
            ->where('due_date', '>=', $filter['from'])
            ->where('due_date', '<=', $filter['to'])
            ->orderBy('due_date');

        
        if (isset($filter['status']) && in_array($filter['status'], ['open', 'paid'])) {
            $status = $filter['status'] == 'open' ? false : true;
            $result->where('paid', $status);
        }
       
        return $result->get();
    }

     /**
     * Returns accounts scheduling for a given category type and a given user
     * for the current date
     * 
     * @param User $user
     * @param int $categoryType
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getAccountsByUserForCron($user, $categoryType)
    {
        return $this->model::select('accounts_schedulings.*')
            ->withoutGlobalScopes()
            ->join('categories', 'categories.id', '=', 'accounts_schedulings.category_id')
            ->where('categories.type', $categoryType)
            ->where('due_date', now()->format('Y-m-d'))
            ->where('paid', false)
            ->where('accounts_schedulings.user_id', $user->id)
            ->get();
    }

    public function deleteAccountEntry($account_scheduling)
    {
        return $account_scheduling->accountEntry()->delete();
    }
}
