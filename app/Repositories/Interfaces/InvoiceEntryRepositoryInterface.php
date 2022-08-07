<?php

namespace App\Repositories\Interfaces;

interface InvoiceEntryRepositoryInterface
{
    public function create(array $data);
    public function update($category, array $data);
    public function delete($category);
    public function findById($id);
    public function getTotalByCategoryForChart(array $filter, $category_type): array;
    public function getTotalByCategoryTypeForRangeDate($categoryType, array $filter);
    public function getEntriesByCategoryAndRangeDate($from, $to, $category_id);
    public function getTotalMonthlyByCategory($categoryType, $date): float;
    public function getEntries($invoice);
    public function createInvoiceEntryParcel($entry, array $data);
}