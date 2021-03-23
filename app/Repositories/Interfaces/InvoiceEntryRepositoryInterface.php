<?php

namespace App\Repositories\Interfaces;

interface InvoiceEntryRepositoryInterface
{
    public function getTotalByCategoryForChart(array $filter, $category_type): array;
    public function getTotalByCategoryTypeForRangeDate($categoryType, array $filter): array;
    public function getEntriesByCategoryAndRangeDate($from, $to, $category_id): array;
    public function getTotalMonthlyByCategory($categoryType, $date): float;
    public function getEntries($invoice);
    public function createInvoiceEntryParcel($entry, array $data);
}