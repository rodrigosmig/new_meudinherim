<?php

namespace App\Repositories\Core\Eloquent;

use App\Models\Account;
use App\Repositories\Core\BaseEloquentRepository;
use App\Repositories\Interfaces\AccountRepositoryInterface;

class AccountRepository extends BaseEloquentRepository implements AccountRepositoryInterface
{
    protected $model = Account::class;

    public function getAccountsForForm() 
    {
        return 'aaaa';
    }

    /**
     * Returns user accounts
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getAccounts() 
    {
        return auth()->user()->accounts;
    }

    public function getTypeList()
    {
        $types = [];

        foreach ($this->model::ARRAY_TYPES as $key => $type) {
            $types[$key] = __('global.' . $type);
        }

        return $types;
    }
    
    /**
     * Returns all entries from a given date
     *
     * @param string $date
     * @return Illuminate\Database\Eloquent\Collection
     */  
    public function getEntriesFromDate($account, $date)
    {
        return $account->entries()->where('date', '>=', $date)->get();
    }

    /**
     * Returns the last balance before the given date
     * 
     * @param Account $account
     * @param string $date
     * @return AccountBalance
     */  
    public function getLastBalance($account, $date)
    {
        $balance = $account->balances()
                    ->where('date', '<', $date)
                    ->orderByDesc('date')
                    ->first();
        
        if (!$balance) {
            $balance = $this->createBalance($account, $date, 0);
        }

        return $balance;
    }

    /**
     * Creates an account balance
     *
     * @param Account $account
     * @param string $date
     * @param int $current_balance
     * @param int $previous_balance
     * @return AccountBalance
     */  
    public function createBalance($account, $date, $current_balance, $previous_balance = 0)
    {
        return $account->balances()->create([
            'date'              => $date,
            'previous_balance'  => $previous_balance,
            'current_balance'   => $current_balance
        ]);
    }

    /**
     * Returns the balance for the given date
     *
     * @param Account $account
     * @return AccountBalance
     */  
    public function findBalanceByDate($account, $date)
    {
        $balance = $account->balances()
                    ->where('date', $date)
                    ->first();

        if (!$balance) {
            $balance = $this->createBalance($account, $date, 0);
        }

        return $balance;
    }

    /**
     * Delete the balances after the date provided 
     *
     * @param string $date
     * @return void
     */  
    public function deleteNextBalances($account, $date): void
    {
        $balances = $account->balances()
            ->where('date', '>=', $date)
            ->get();
        
        $ids = [];

        foreach ($balances as $balance) {
            $ids[] = $balance->id;
        }

        $account->balances()->whereIn('id', $ids)->delete();
    }
}