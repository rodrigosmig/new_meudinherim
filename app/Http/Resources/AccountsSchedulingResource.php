<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AccountsSchedulingResource extends JsonResource
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
        $tags = [];

        if($this->tags) {
            foreach ($this->tags as $tag) {
                $tags[] = $tag->name;
            }
        }

        return [
            "id" => $this->id,
            "due_date" => $this->due_date,
            "paid_date" => $this->paid_date,
            "description" => $this->description,
            "value" => $this->value,
            "category" => [
                "id"    => $this->category->id,
                "type"  => $this->category->type,
                "name"  => $this->category->name
            ],
            "invoice" => !$this->invoice_id ? null : ["invoice_id" => $this->invoice->id, "card_id" => $this->invoice->card->id],
            "paid" => $this->paid,
            "monthly" => $this->monthly ? true : false,
            "has_parcels" => $this->has_parcels ?? false,
            "is_parcel" =>  $this->isParcel(),
            "total_purchase" => $this->isParcel() ? $this->parcelable->value : null,
            "parcel_number" => $this->isParcel() ? $this->parcel_number : null,
            "parcelable_id" => $this->isParcel() ? $this->parcelable_id : null,
            "tags"          => $tags,
            "created_at" => $this->created_at->format('Y-m-d'),
        ];
    }
}
