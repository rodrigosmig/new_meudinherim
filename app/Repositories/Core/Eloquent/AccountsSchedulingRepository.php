<?php

namespace App\Repositories\Core\Eloquent;

use DateTime;
use App\Models\AccountsScheduling;
use App\Models\Parcel;
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
            ->where('has_parcels', false)
            ->orderBy('due_date');


        if (isset($filter['status']) && $filter['status']) {
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
            ->where('has_parcels', false)
            ->where('paid', false)
            ->where('accounts_schedulings.user_id', $user->id)
            ->get();
    }

    /**
     * Delete the account entry related to account scheduling 
     * 
     * @param mixed $account_scheduling
     * @return bool
     */
    public function deleteAccountEntry($account_scheduling): bool
    {
        return $account_scheduling->accountEntry()->delete();
    }

    public function createParcels($account_scheduling, array $data)
    {
        return $account_scheduling->parcels()->create([
            'due_date'      => $data['due_date'],
            'description'   => $data['description'],
            'value'         => $data['parcel_value'],
            'parcel_number' => $data['parcel_number'],
            'parcel_total'  => $data['total_parcels'],
            'category_id'   => $data['category_id'],
        ]);
    }

     /**
     * Delete parcels for a given account scheduling
     *
     * @param AccountsScheduling $invoice
     * @return void
     */
    public function deleteParcels($account_scheduling): void
    {
        foreach ($account_scheduling->parcels as $parcel) {
            $parcel->delete();
        }
    }

    /**
     * Creates the account payables to next month
     *
     * @param AccountsScheduling $account_scheduling
     * @return void
     */
    public function createMonthlyPayment($account_scheduling): void
    {
        $date = new DateTime($account_scheduling->due_date);
        $next_month = $date->modify('+1 month');

        $account_scheduling->create([
            'due_date'      => $next_month->format('Y-m-d'),
            'description'   => $account_scheduling->description,
            'value'         => $account_scheduling->value,
            'category_id'   => $account_scheduling->category_id,
            'monthly'       => $account_scheduling->monthly
        ]);
    }

    /**
     * Returns the account scheduling of the next month
     *
     * @param AccountsScheduling $account_scheduling
     * @return AccountsScheduling
     */
    public function getNextAccountScheduling($account_scheduling)
    {
        $date = new DateTime($account_scheduling->due_date);
        $next_month = $date->modify('+1 month');

        return $this->model::where('due_date', $next_month->format('Y-m-d'))
            ->where('description', $account_scheduling->description)
            ->where('category_id', $account_scheduling->category_id)
            ->where('monthly', $account_scheduling->monthly)
            ->where('has_parcels', $account_scheduling->has_parcels)
            ->where('value', $account_scheduling->value * 100)
            ->first();
    }
}
