<?php

namespace App\Repositories\Interfaces;

interface ParcelRepositoryInterface
{
    public function findById($id);
    public function getParcelsOfInvoice($invoice);
    public function getParcelsOfAccountsScheduling(int $categoryType, array $range_date);
    public function findParcelsOfAccountsScheduling($account_scheduling_id, $parcel_id);
    public function findParcelOfInvoiceEntry($invoice_entry_id, $parcel_id);
    public function getOpenParcels($invoice_entry, int $parcel_number);
    public function getTotalByCategoryTypeForRangeDate($category_type, array $filter);
}