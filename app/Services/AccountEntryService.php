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

        if (! $account_entry) {
            return false;
        }
        
        $account_entry->update($data);
        
        return $account_entry;
    }

    public function delete($id)
    {
        $account_entry = $this->findById($id);

        if (! $account_entry) {
            return false;
        }

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
     * @param string $range_date
     * @return Illuminate\Database\Eloquent\Collection
     */  
    public function getEntriesByAccount($account_id, $range_date = null)
    {
        $from = date('Y-m-1');
        $to = date('Y-m-t');

        if ($range_date && isset($range_date['from']) && isset($range_date['to'])) {
            $from = $range_date['from'];
            $to = $range_date['to'];
        }

        return $this->account_entry
            ->where('account_id', $account_id)
            ->where('date', '>=', $from)
            ->where('date', '<=', $to)
            ->orderBy('date')
            ->get();
    }
}
