<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmergencyResource extends JsonResource
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
            'type' => $this->type,
            'severity' => $this->severity,
            'description' => $this->description,
            'location' => $this->location,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'contact_name' => $this->contact_name,
            'contact_phone' => $this->contact_phone,
            'contact_relationship' => $this->contact_relationship,
            'medical_conditions' => $this->medical_conditions,
            'accessibility_needs' => $this->accessibility_needs,
            'status' => $this->status,
            'resolved_at' => $this->resolved_at,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'profile_photo' => $this->user->profile_photo,
                ];
            }),
            'media' => $this->whenLoaded('media', function () {
                return $this->media->map(function ($media) {
                    return [
                        'id' => $media->id,
                        'file_name' => $media->file_name,
                        'mime_type' => $media->mime_type,
                        'size' => $media->size,
                        'url' => $media->getUrl(),
                    ];
                });
            }),
            'responses' => $this->whenLoaded('responses', function () {
                return $this->responses->map(function ($response) {
                    return [
                        'id' => $response->id,
                        'responder_type' => $response->responder_type,
                        'responder_name' => $response->responder_name,
                        'response_time' => $response->response_time,
                        'notes' => $response->notes,
                        'created_at' => $response->created_at,
                    ];
                });
            }),
        ];
    }
}
