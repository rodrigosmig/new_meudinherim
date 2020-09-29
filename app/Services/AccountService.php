<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Account;
use App\Models\Category;
use App\Models\AccountEntry;
use App\Models\AccountBalance;

class AccountService
{
    protected $account;

    public function __construct(Account $account)
    {
        $this->account = $account;
    }

    public function store(array $data)
    {
        return $this->account->create($data);
    }

    public function update($id, array $data)
    {
        $account = $this->findById($id);

        if (! $account) {
            return false;
        }

        return $account->update($data);
    }


    public function delete($id)
    {
        $account = $this->findById($id);

        if (! $account) {
            return false;
        }

        return $account->delete();
    }

    public function findById($id)
    {
        return $this->account->find($id);
    }

    /**
     * Get a list of accounts for the form
     *
     * @return array
     */
    public function getAccountsForForm()
    {
        return auth()->user()->accounts()->pluck('name', 'id');
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

    /**
     * Returns a list of account types
     *
     * @return array
     */
    public function getTypeList(): array
    {
        $types = [];

        foreach ($this->account::ARRAY_TYPES as $key => $type) {
            $types[$key] = __('global.' . $type);
        }

        return $types;
    }

    /**
     * Updates account balance
     *
     * @param Account $account
     * @param string $date
     * @return void
     */  
    public function updateBalance(Account $account, $date)
    {
        $this->account = $account;

        $entries = $this->getEntriesFromDate($date);
        $balances = $this->getTotalBalanceOfDay($entries);

        $last_date = Carbon::createFromFormat('Y-m-d', $date)->sub('1 day')->format('Y-m-d');
        $last_balance = $this->findBalanceByDate($last_date);
       
        $previous_balance = $last_balance->current_balance;

        if ($entries->isEmpty()) {
            $balance = $this->findBalanceByDate($date);
            if ($balance) {
                $balance->delete();
            }
        }
        
        foreach ($balances as $date => $value) {
            $balance = $this->findBalanceByDate($date);
            $current_balance = $previous_balance + $value;

            if (!$balance) {                
                $balance = $this->createBalance($date, $current_balance, $previous_balance);
            } else {
                $balance->current_balance = $current_balance;
            }

            $balance->previous_balance = $previous_balance;
            $balance->save();

            $previous_balance = $current_balance;
        }
    }

    /**
     * Returns all entries from a given date
     *
     * @param string $date
     * @return Illuminate\Database\Eloquent\Collection
     */  
    private function getEntriesFromDate($date)
    {
        return $this->account->entries()->where('date', '>=', $date)->get();
    }

    /**
     * Returns an array with balances separated by day
     *
     * @param Illuminate\Database\Eloquent\Collection $entries
     * @return array
     */  
    private function getTotalBalanceOfDay($entries): array
    {
        $total = [];

        foreach ($entries as $entry) {
            if (! isset($total[$entry->date])) {
                $total[$entry->date] = 0;
            }

            if ($entry->category->type === Category::INCOME) {
                $total[$entry->date] += $entry->value;
            } else {
                $total[$entry->date] -= $entry->value;
            }
        }

        ksort($total);

        return $total;
    }

    /**
     * Returns the balance for the given date
     *
     * @param Account $account
     * @return AccountBalance
     */  
    private function findBalanceByDate($date): AccountBalance
    {
        $balance = $this->account->balances()->where('date', $date)->first();

        if (!$balance) {
            $balance = $this->createBalance($date, 0, 0);
        }

        return $balance;
    }

    /**
     * Creates an account balance
     *
     * @param string $date
     * @param int $current_balance
     * @param int $previous_balance
     * @return AccountBalance
     */  
    private function createBalance($date, $current_balance, $previous_balance = 0): AccountBalance
    {
        return $this->account->balances()->create([
            'date'              => $date,
            'previous_balance'  => $previous_balance,
            'current_balance'   => $current_balance
        ]);
    }
}
