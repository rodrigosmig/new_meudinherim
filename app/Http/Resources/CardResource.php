<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CardResource extends JsonResource
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
            "id" => $this->id,
            "name" => $this->name,
            "pay_day" => $this->pay_day,
            "closing_day" => $this->closing_day,
            "credit_limit" => $this->credit_limit,
            "balance" => $this->balance
        ];
    }
}
