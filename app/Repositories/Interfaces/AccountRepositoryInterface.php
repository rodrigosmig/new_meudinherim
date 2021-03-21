<?php

namespace App\Repositories\Interfaces;

interface AccountRepositoryInterface
{
    public function getAccountsForForm();
    public function getAccounts();
    public function getTypeList();
    public function getEntriesFromDate($account, string $date);
    public function getLastBalance($account, string $date);
    public function createBalance($account, $date, $current_balance, $previous_balance = 0);
    public function findBalanceByDate($account, $date);
    public function deleteNextBalances($account, $date): void;
}