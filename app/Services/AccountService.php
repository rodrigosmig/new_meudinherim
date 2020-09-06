<?php

namespace App\Services;

use App\Models\Account;
use App\Models\AccountEntry;

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
}
