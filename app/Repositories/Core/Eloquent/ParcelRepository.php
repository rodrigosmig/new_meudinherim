<?php

namespace App\Repositories\Core\Eloquent;

use App\Models\Parcel;
use App\Models\InvoiceEntry;
use Illuminate\Database\Eloquent\Builder;
use App\Repositories\Core\BaseEloquentRepository;
use App\Repositories\Interfaces\ParcelRepositoryInterface;

class ParcelRepository extends BaseEloquentRepository implements ParcelRepositoryInterface
{
    protected $model = Parcel::class;

    /**
     * Returns entries for a given invoice
     *
     * @param Invoice $invoice
     * @return Illuminate\Database\Eloquent\Collection
     */ 
    public function getParcelsOfInvoice($invoice)
    {
        return $this->model::whereHasMorph(
            'parcelable', 
            InvoiceEntry::class,
            function (Builder $query) use ($invoice) {
                $query->where('parcels.invoice_id', '=', $invoice->id);
            })->get();
    }
}
