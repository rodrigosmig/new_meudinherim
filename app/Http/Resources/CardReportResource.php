<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CardReportResource extends JsonResource
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
            'id' => $this->id,
            'date' => $this->date,
            'description' => $this->description,
            'value' => $this->value,
            'category' => [
                'id'    => $this->category->id,
                'name'  => $this->category->name
            ],
            'source' => $this->invoice->card->name            
        ];
    }
}
