<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AccountEntryResource extends JsonResource
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
        $accountScheduling = null;
        $tags = [];

        if($this->tags) {
            foreach ($this->tags as $tag) {
                $tags[] = $tag->name;
            }
        }

        if ($this->accountScheduling) {
            $accountScheduling = [
                'is_parcel'     => $this->accountScheduling->isParcel(),
                "id"            => $this->accountScheduling->id,
                'parcelable_id' => $this->accountScheduling->isParcel() ? $this->accountScheduling->parcelable_id : null,
                "due_date"      => $this->accountScheduling->due_date,
                "paid_date"     => $this->accountScheduling->paid_date,
            ];
        } elseif ($this->parcel) {
            $accountScheduling = [
                'is_parcel'     => $this->parcel->isParcel(),
                "id"            => $this->parcel->id,
                'parcelable_id' => $this->parcel->isParcel() ? $this->parcel->parcelable_id : null,
                "due_date"      => $this->parcel->due_date,
                "paid_date"     => $this->parcel->paid_date,
            ];
        }

        return [
            "id" => $this->id,
            "date" => $this->date,
            "description" => $this->description,
            "value" => $this->value,
            "category" => [
                'id'    => $this->category->id,
                'name'  => $this->category->name,
                'type'  => $this->category->type
            ],
            "account_scheduling"    => $accountScheduling,
            "account"               => [
                "id"    => $this->account->id,
                "name"  => $this->account->name,
                "type"  => $this->account->type
            ],
            "tags"      => $tags,
            "created_at"            => $this->created_at,
        ];
    }
}
