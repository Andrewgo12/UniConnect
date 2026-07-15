<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
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
            'content' => $this->content,
            'type' => $this->type,
            'status' => $this->status,
            'vibration_pattern' => data_get($this->metadata, 'vibration_pattern', []),
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
            'conversation' => $this->whenLoaded(
                'conversation',
                fn () => [
                    'id' => $this->conversation->id,
                    'title' => $this->conversation->title,
                ],
                [
                    'id' => $this->conversation_id,
                    'title' => $this->conversation?->title,
                ]
            ),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
