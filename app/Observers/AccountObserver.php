<?php

namespace App\Observers;

use App\Models\Account;

class AccountObserver
{
    /**
     * Handle the account "created" event.
     *
     * @param  \App\Models\Account  $account
     * @return void
     */
    public function creating(Account $account)
    {
        $account->user_id = auth()->user()->id;
    }

    /**
     * Handle the account "updated" event.
     *
     * @param  \App\Models\Account  $account
     * @return void
     */
    public function updating(Account $account)
    {
        $account->user_id = auth()->user()->id;
    }
}
