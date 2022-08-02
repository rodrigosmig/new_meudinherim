<?php

namespace App\Http\Resources;

use App\Models\Account;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
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
            'id'            => $this->id,
            'name'          => $this->name,
            'type'          => [
                'id'    => $this->type,
                'desc'  => toCategoryType($this->type)
            ],
            'balance'       => $this->balance,
            'active'        => $this->active,
            'created_at'    => $this->created_at
        ];
    }
}
