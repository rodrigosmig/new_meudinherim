<?php

namespace App\Services;

use App\Models\AccountsScheduling;

class AccountsSchedulingService
{
    protected $account_scheduling;

    public function __construct(AccountsScheduling $account_scheduling)
    {
        $this->account_scheduling = $account_scheduling;
    }
}