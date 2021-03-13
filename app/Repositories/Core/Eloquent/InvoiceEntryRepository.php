<?php

namespace App\Repositories\Core\Eloquent;

use App\Models\InvoiceEntry;
use App\Repositories\Core\BaseEloquentRepository;
use App\Repositories\Interfaces\InvoiceEntryRepositoryInterface;

class InvoiceEntryRepository extends BaseEloquentRepository implements InvoiceEntryRepositoryInterface
{
    protected $model = InvoiceEntry::class;

}