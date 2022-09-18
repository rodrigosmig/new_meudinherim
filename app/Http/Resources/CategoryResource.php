<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            'id'                => $this->id,
            'type'              => $this->type,
            'name'              => $this->name,
            'active'            => $this->active,
            'show_in_dashboard' => $this->show_in_dashboard,
            'created_at'        => $this->created_at
        ];
    }
}
