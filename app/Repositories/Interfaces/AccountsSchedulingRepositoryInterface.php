<?php

namespace App\Repositories\Interfaces;

interface AccountsSchedulingRepositoryInterface
{
    public function getAccountsSchedulingsByType($categoryType, array $filter = []);
    public function getAccountsByUserForCron($user, $categoryType);
    public function deleteAccountEntry($account_scheduling);
}