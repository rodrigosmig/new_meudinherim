<?php

namespace App\Repositories\Interfaces;

interface AccountsSchedulingRepositoryInterface
{
    public function getAccountsSchedulingsByType($categoryType, array $filter = []);
    public function getAccountsByUserForCron($user, $categoryType);
    public function deleteAccountEntry($account_scheduling): bool;
    public function createParcels($account_scheduling, array $data);
    public function createMonthlyPayment($account_scheduling): void;
    public function deleteParcels($account_scheduling): void;
    public function getNextAccountScheduling($account_scheduling);
}