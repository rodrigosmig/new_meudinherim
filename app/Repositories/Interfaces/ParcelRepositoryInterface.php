<?php

namespace App\Repositories\Interfaces;

interface ParcelRepositoryInterface
{
    public function getParcelsOfInvoice($invoice);
}