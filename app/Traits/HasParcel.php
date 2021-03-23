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
}