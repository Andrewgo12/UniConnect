<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnalyticsResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'event_type' => $this->event_type,
            'category' => $this->category,
            'action' => $this->action,
            'resource_type' => $this->resource_type,
            'resource_id' => $this->resource_id,
            'value' => $this->value,
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
