<?php

namespace App\Services;

use Exception;
use App\Models\Account;
use App\Models\Category;
use App\Models\AccountBalance;
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
    public function getAccounts() 
    {
        return $this->repository->getAccounts();
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
     * @param array $data
     * @return void
     * @throws Exception
     */  
    public function accountTransfer($data)
    {
        $source_account     = $this->repository->findById($data['source_account_id']);
        $destination_account    = $this->repository->findById($data['destination_account_id']);

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
        $accounts   = $this->repository->getAccounts();
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
