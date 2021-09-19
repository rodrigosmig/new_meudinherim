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
        $accountScheduling = "";

        if ($this->accountScheduling ) {
            $accountScheduling = [
                "id"            => $this->accountScheduling->id,
                "due_date"      => $this->accountScheduling->due_date,
                "paid_date"     => $this->accountScheduling->paid_date,
                "monthly"       => $this->accountScheduling->monthly
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
            "created_at"            => $this->created_at,
        ];
    }
}
