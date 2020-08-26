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

    public function update($id, array $data)
    {
        $account = $this->findById($id);

        return $account->update($data);
    }


    public function delete($id)
    {
        $account = $this->findById($id);

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
        return $this->account::where('user_id', auth()->user()->id)->pluck('name', 'id');
    }

    /**
     * Returns user accounts
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getAccountsByUser() 
    {

        return $this->account::where('user_id', auth()->user()->id)->get();
    }

    /**
     * Returns a list of account types
     *
     * @return array
     */
    public function getTypeList(): array
    {
        $types = [];

        foreach ($this->account::TYPES as $key => $type) {
            $types[$key] = __('global.' . $type);
        }

        return $types;
    }

    /**
     * Add the entries to the account
     *
     * @param Account $account
     * @param array $data
     * @return AccountEntry
     */ 
    public function addEntry(Account $account, $data): AccountEntry
    {
        $data['user_id'] = auth()->user()->id;

        return $account->entries()->create($data);
    }
}
