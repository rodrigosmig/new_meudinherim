<?php

namespace App\Repositories\Interfaces;

interface AccountEntryRepositoryInterface
{
    public function getEntriesByAccountId($account_id, array $range_date);
    public function getTotalTypeOfCategory($categoryType, $month, $year): int;
    public function getTotalByCategory($categoryType, $month, $year): array;
    public function getTotalByCategoryTypeForRangeDate($categoryType, array $filter): array;
    public function getEntriesByCategoryAndRangeDate($from, $to, $category_id);
}