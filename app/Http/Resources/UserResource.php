<?php

namespace App\Http\Resources;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'id'                    => $this->id,
            'name'                  => $this->name,
            'email'                 => $this->email,
            'avatar'                => $this->hasAvatar() ? url("storage/{$this->avatar}") : '',
            'enable_notification'   => $this->enable_notification ? true : false,
            'hasEmailVerified'      => true
        ];
    }
}
