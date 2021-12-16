<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceEntryResource extends JsonResource
{
    public static $wrap = null;
    
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id"            => $this->id,
            "date"          => $this->date,
            "description"   => $this->description,
            "value"         => $this->value,
            "category"      => [
                'id'    => $this->category->id,
                'name'  => $this->category->name,
                'type'  => $this->category->type
            ],
            'card_id'           => $this->invoice->card->id,
            'invoice_id'        => $this->invoice->id,
            'is_parcel'         => $this->isParcel(),
            "parcel_number"     => $this->isParcel() ? $this->parcel_number : null,
            'parcel_total'      => $this->isParcel() ? $this->parcel_total : null,
            "total_purchase"    => $this->isParcel() ? $this->parcelable->value : null,
            "parcelable_id"     => $this->isParcel() ? $this->parcelable_id : null,
            "anticipated"       => $this->anticipated,
            "created_at"        => $this->created_at,
        ];
    }
}
