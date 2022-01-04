<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
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
            "due_date"      => $this->due_date,
            "closing_date"  => $this->closing_date,
            "amount"        => $this->amount,
            "paid"          => $this->isPaid(),
            "isClosed"      => $this->isClosed(),
            'hasPayable'    => $this->payable ? true : false,
            "card"          => [
                'id'    => $this->card->id,
                'name'  => $this->card->name
            ]
        ];
    }
}
