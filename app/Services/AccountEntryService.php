<?php

namespace App\Services;

use App\Models\Account;
use App\Models\AccountEntry;

class AccountEntryService
{
    protected $account_entry;

    public function __construct(AccountEntry $account_entry, Account $account)
    {
        $this->account          = $account;
        $this->account_entry    = $account_entry;
    }

    /**
     * Creates the entries to the account
     *
     * @param Account $account
     * @param array $data
     * @return AccountEntry
     */ 
    public function make(Account $account, array $data): AccountEntry
    {
        return $account->entries()->create($data);
    }

    public function update($id, array $data)
    {
        $account_entry = $this->findById($id);
        
        $account_entry->update($data);
        
        return $account_entry;
    }

    public function delete($id)
    {
        $account_entry = $this->findById($id);

        return $account_entry->delete();
    }

    public function findById($id)
    {
        return $this->account_entry->find($id);
    }

    /**
     * Returns account entries according to the given account id
     *
     * @param int $account_id
     * @return Illuminate\Database\Eloquent\Collection
     */  
    public function getEntriesByAccount($account_id)
    {
        return $this->account_entry
            ->where('account_id', $account_id)
            ->where('user_id', auth()->user()->id)
            ->get();
    }

    /**
     * Returns an array with separate entries per account
     *
     * @return array
     */  
    public function  getEntriesForAccountStatement(): array
    {
        $accounts = $this->account->where('user_id', auth()->user()->id)->pluck('name', 'id');
        
        $entries = [];

        foreach ($accounts as $key => $account) {
            $entries_account = $this->getEntriesByAccount($key);
            $entries[$key]['name']  = $account;
            $entries[$key]['items'] = $entries_account;
        }

        return $entries;
    }
}
