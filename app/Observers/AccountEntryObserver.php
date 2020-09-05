<?php

namespace App\Observers;

use App\Models\AccountEntry;

class AccountEntryObserver
{
    /**
     * Handle the account entry "created" event.
     *
     * @param  \App\Models\AccountEntry  $accountEntry
     * @return void
     */
    public function creating(AccountEntry $accountEntry)
    {
        $accountEntry->user_id = auth()->user()->id;
    }

    /**
     * Handle the account entry "updated" event.
     *
     * @param  \App\Models\AccountEntry  $accountEntry
     * @return void
     */
    public function updating(AccountEntry $accountEntry)
    {
        $accountEntry->user_id = auth()->user()->id;
    }
}
