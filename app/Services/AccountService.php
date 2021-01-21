<?php

namespace App\Services;

use Exception;
use App\Models\Account;
use App\Models\Category;
use App\Models\AccountBalance;
use App\Services\AccountEntryService;

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

        $last_balance = $this->getLastBalance($date);
       
        $previous_balance = $last_balance->current_balance;

        if ($entries->isEmpty()) {
            $balance = $this->findBalanceByDate($date);
            if ($balance) {
                $balance->delete();
            }
        }

        $this->deleteNextBalances($date);

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
            $balance = $this->createBalance($date, 0);
        }

        return $balance;
    }

    /**
     * Returns the last balance before the given date
     *
     * @param string $date
     * @return AccountBalance
     */  
    private function getLastBalance($date): AccountBalance
    {
        $balance = $this->account->balances()
            ->where('date', '<', $date)
            ->orderByDesc('date')
            ->first();
        
        if (!$balance) {
            $balance = $this->createBalance($date, 0);
        }

        return $balance;
    }

    /**
     * Returns the last balance before the given date
     *
     * @param string $date
     * @return void
     */  
    private function deleteNextBalances($date)
    {
        $balances = $this->account->balances()
            ->where('date', '>=', $date)
            ->get();
        
        $ids = [];

        foreach ($balances as $balance) {
            $ids[] = $balance->id;
        }

        $this->account->balances()->whereIn('id', $ids)->delete();
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

    /**
     * Transfers an amount between bank accounts
     *
     * @param array $data
     * @return void
     * @throws Exception
     */  
    public function accountTransfer($data)
    {
        $source_account     = $this->findById($data['source_account_id']);
        $destination_account    = $this->findById($data['destination_account_id']);

        if ($source_account->id === $destination_account->id) {
            throw new Exception(__('messages.accounts.equal_accounts'));
        }

        $newData = $this->prepareDataForEntry($data);

        $accountEntryService = app(AccountEntryService::class);

        $accountEntryService->make($source_account, $newData['source']);
        $accountEntryService->make($destination_account, $newData['destination']);

        $this->updateBalance($source_account, $data['date']);
        $this->updateBalance($destination_account, $data['date']);
    }

    private function prepareDataForEntry($data): array
    {
        $newData['source'] = [
            "account_id" => $data['source_account_id'],
            "category_id" => $data['source_category_id'],
            "date" => $data['date'],
            "description" => $data['description'],
            "value" => $data['value'],
        ];

        $newData['destination'] = [
            "account_id" => $data['destination_account_id'],
            "category_id" => $data['destination_category_id'],
            "date" => $data['date'],
            "description" => $data['description'],
            "value" => $data['value'],
        ];

        return $newData;
    }

    /**
     * returns the balance of all user accounts
     *
     * @return array
     */  
    public function getAllAccountBalances(): array
    {
        $accounts   = $this->account->get();
        $balances   = [];
        $total      = 0; 

        foreach ($accounts as $account) {
            $balances[] = [
                'account_id'    => $account->id,
                'account_name'  => $account->name,
                'balance'       => $account->balance
            ];
            $total += $account->balance;
        }

        $balances['total'] = $total;
        return $balances;
    }
}
