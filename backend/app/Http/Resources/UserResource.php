<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        if (! $this->relationLoaded('profile')) {
            $this->load('profile');
        }

        $profile = $this->profile;
        $preferences = $profile?->preferences ?? [];

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'accessibility_needs' => $this->accessibility_needs ?? [],
            'profile' => [
                'id' => $profile?->id,
                'name' => $profile?->name,
                'avatar' => $profile?->avatar,
                'bio' => data_get($preferences, 'bio'),
                'preferences' => $preferences,
                'accessibility_settings' => [
                    'blind' => (bool) ($profile?->blind ?? false),
                    'deaf' => (bool) ($profile?->deaf ?? false),
                    'mute' => (bool) ($profile?->mute ?? false),
                ],
                'blind' => (bool) ($profile?->blind ?? false),
                'deaf' => (bool) ($profile?->deaf ?? false),
                'mute' => (bool) ($profile?->mute ?? false),
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
