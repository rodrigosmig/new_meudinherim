<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $type = isset($this->data['payables']) ? 'payables' : 'receivables';
        
        return [
            'id'    => $this->id,
            'type'  => $type,
            'data'  => [
                "id"            => $this->data[$type]['id'],
                "due_date"      => $this->data[$type]['due_date'],
                "description"   => $this->data[$type]['description'],
                "value"         => $this->data[$type]['value'],
                "is_parcel"     => isset($this->data[$type]['parcelable_id']) ? true : false,
                "parcelable_id" => $this->data[$type]['parcelable_id'] ?? null
            ]
        ];
    }
}
