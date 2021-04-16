<?php

namespace App\Repositories\Interfaces;

interface ParcelRepositoryInterface
{
    public function getParcelsOfInvoice($invoice);
    public function getParcelsOfAccountsScheduling(int $categoryType, array $range_date);
    public function findParcelsOfAccountsScheduling($account_scheduling_id, $parcel_id);
}