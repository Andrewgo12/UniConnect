<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnalyticsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'event_type' => $this->event_type,
            'category' => $this->category,
            'action' => $this->action,
            'resource_type' => $this->resource_type,
            'resource_id' => $this->resource_id,
            'value' => $this->value,
            'metadata' => $this->metadata,
            'ip_address' => $this->ip_address,
            'user_agent' => $this->user_agent,
            'device_type' => $this->device_type,
            'platform' => $this->platform,
            'language' => $this->language,
            'accessibility_mode' => $this->accessibility_mode,
            'response_time' => $this->response_time,
            'error_code' => $this->error_code,
            'success' => $this->success,
            'location' => $this->location,
            'timezone' => $this->timezone,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'profile_photo' => $this->user->profile_photo,
                ];
            }),
        ];
    }
}
