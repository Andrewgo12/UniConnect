<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'type' => $this->type,
            'status' => $this->status,
            'metadata' => $this->metadata,
            'creator' => $this->when($this->created_by, function () {
                return [
                    'id' => $this->created_by,
                    'name' => $this->relationLoaded('creator')
                        ? $this->creator->name
                        : optional($this->creator)->name,
                ];
            }),
            'participants' => $this->whenLoaded(
                'participants',
                function () {
                    return $this->participants->map(function ($participant) {
                        return [
                            'id' => $participant->id,
                            'name' => $participant->name,
                            'avatar' => $participant->avatar ?? null,
                            'pivot' => $participant->pivot,
                        ];
                    });
                },
                []
            ),
            'last_message' => $this->whenLoaded('lastMessage', function () {
                if (! $this->lastMessage) {
                    return null;
                }

                return [
                    'id' => $this->lastMessage->id,
                    'content' => $this->lastMessage->content,
                    'type' => $this->lastMessage->type,
                    'created_at' => $this->lastMessage->created_at,
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
