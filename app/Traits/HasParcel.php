<?php

namespace App\Traits;

use App\Models\Parcel;

trait HasParcel
{
    public function parcels()
    {
        return $this->morphMany(Parcel::class, 'parcelable');
    }

    /**
     * Checks if the entry has parcels
     *
     * @return bool
     */
    public function hasParcels(): bool
    {
        return $this->has_parcels == true;
    }

    /**
     * Checks if is a parcel
     *
     * @return bool
     */
    public function isParcel()
    {
        return isset($this->parcelable_type) 
            && isset($this->parcelable_id) 
            && ($this->parcelable_type === AccountsScheduling::class || $this->parcelable_type === InvoiceEntry::class);
    }
}