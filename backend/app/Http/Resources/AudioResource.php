<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AudioResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $duration = (int) ($this->duration ?? 0);
        $minutes = intdiv($duration, 60);
        $seconds = $duration % 60;

        return [
            'id' => $this->id,
            'title' => $this->title,
            'type' => $this->type,
            'duration' => $this->duration,
            'formatted_duration' => sprintf('%02d:%02d', $minutes, $seconds),
            'language' => $this->language,
            'formatted_size' => $this->getFormattedSize(),
            'metadata' => $this->metadata,
            'user' => $this->whenLoaded(
                'user',
                fn () => [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                ],
                [
                    'id' => $this->user_id,
                    'name' => $this->user?->name,
                ]
            ),
            'created_at' => $this->created_at,
        ];
    }
}
