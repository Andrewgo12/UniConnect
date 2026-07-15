<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'email'      => $this->email,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'profile'    => $this->whenLoaded('profile', fn() => [
                'id'          => $this->profile->id,
                'name'        => $this->profile->name,
                'avatar'      => $this->profile->avatar,
                'blind'       => $this->profile->blind,
                'deaf'        => $this->profile->deaf,
                'mute'        => $this->profile->mute,
                'preferences' => $this->profile->preferences,
            ]),
        ];
    }
}
