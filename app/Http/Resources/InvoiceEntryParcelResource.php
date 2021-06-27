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
            'parcel_number' => $this->parcel_number,
            'parcel_total'  => $this->parcel_total,
            "category" => [
                'id'    => $this->category->id,
                'name'  => $this->category->name,
                'type'  => $this->category->type
            ],
            "created_at" => $this->created_at
        ];
    }
}
