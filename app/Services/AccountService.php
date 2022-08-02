<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Category;
use App\Services\AccountEntryService;
use App\Repositories\Core\Eloquent\AccountRepository;

class AccountService
{
    protected $repository;

    public function __construct(AccountRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    public function update(Account $account, array $data)
    {
        return $this->repository->update($account, $data);
    }


    public function delete(Account $account)
    {
        return $this->repository->delete($account);
    }

    public function findById($id)
    {
        return $this->repository->findById($id);
    }

    /**
     * Get a list of accounts for the form
     *
     * @return array
     */
    public function getAccountsForForm()
    {
        return $this->repository->getAccountsForForm();
    }

    /**
     * Returns user accounts
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getAccounts(bool $status = true) 
    {
        return $this->repository->getAccounts($status);
    }

    /**
     * Returns a list of account types
     *
     * @return array
     */
    public function getTypeList(): array
    {
        return $this->repository->getTypeList();
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
        $entries = $this->repository->getEntriesFromDate($account, $date);
        $balances = $this->getTotalBalanceOfDay($entries);

        $last_balance = $this->repository->getLastBalance($account, $date);
       
        $previous_balance = $last_balance->current_balance;

        if ($entries->isEmpty()) {
            $balance = $this->repository->findBalanceByDate($account, $date);
            if ($balance) {
                $balance->delete();
            }
        }

        $this->repository->deleteNextBalances($account, $date);

        foreach ($balances as $date => $value) {
            $balance = $this->repository->findBalanceByDate($account, $date);
            $current_balance = $previous_balance + $value;

            if (!$balance) {                
                $balance = $this->repository->createBalance($account, $date, $current_balance, $previous_balance);
            } else {
                $balance->current_balance = $current_balance;
            }

            $balance->previous_balance = $previous_balance;
            $balance->save();

            $previous_balance = $current_balance;
        }
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
     * Transfers an amount between bank accounts
     *
     * @param Account $from_account
     * @param Account $to_account
     * @param array $data
     * @return void
     */  
    public function accountTransfer(Account $from_account, Account $to_account, array $data)
    {
        $newData = $this->prepareDataForEntry($data);

        $accountEntryService = app(AccountEntryService::class);

        $accountEntryService->create($from_account->id, $newData['source']);
        $accountEntryService->create($to_account->id, $newData['destination']);

        $this->updateBalance($from_account, $data['date']);
        $this->updateBalance($to_account, $data['date']);
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
        $accounts   = $this->repository->getAccounts();
        $balances['balances']   = [];
        $total      = 0; 

        foreach ($accounts as $account) {
            $balances['balances'][] = [
                'account_id'    => $account->id,
                'account_name'  => $account->name,
                'balance'       => $account->balance
            ];
            $total += $account->balance;
        }

        $balances['total'] = $total;
        return $balances;
    }

    /**
     * returns the balance of given account
     *
     * @param Account $account
     * @return array
     */  
    public function getAccountBalance($account): array
    {      
        $balances['balances'][] = [
            'account_id'    => $account->id,
            'account_name'  => $account->name,
            'balance'       => $account->balance
        ];

        return $balances;
    }
}
