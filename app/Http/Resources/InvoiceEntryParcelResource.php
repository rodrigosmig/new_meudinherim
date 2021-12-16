<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceEntryParcelResource extends JsonResource
{
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
            "category" => [
                'id'    => $this->category->id,
                'name'  => $this->category->name,
                'type'  => $this->category->type
            ],
            'invoice_id'        => $this->invoice->id,
            'is_parcel'         => $this->isParcel(),
            'parcel_number'     => $this->parcel_number,
            'parcel_total'      => $this->parcel_total,
            "total_purchase"    => $this->parcelable->value,
            "parcelable_id"     => $this->parcelable_id,
            "anticipated"       => $this->anticipated,
            "created_at" => $this->created_at
        ];
    }
}
