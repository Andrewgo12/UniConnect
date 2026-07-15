<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmergencyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'severity' => $this->severity,
            'description' => $this->description,
            'location' => $this->location,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'status' => $this->status,
            'acknowledged_at' => $this->acknowledged_at,
            'resolved_at' => $this->resolved_at,
            'contact_name' => $this->contact_name,
            'contact_phone' => $this->contact_phone,
            'contact_relationship' => $this->contact_relationship,
            'medical_conditions' => $this->medical_conditions,
            'accessibility_needs' => $this->accessibility_needs,
            'metadata' => $this->metadata,
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
