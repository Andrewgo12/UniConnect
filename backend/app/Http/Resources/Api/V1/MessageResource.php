<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
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
            'conversation_id' => $this->conversation_id,
            'user_id' => $this->user_id,
            'content' => $this->content,
            'type' => $this->type,
            'status' => $this->status,
            'priority' => $this->priority,
            'metadata' => $this->metadata,
            'parent_id' => $this->parent_id,
            'edited_at' => $this->edited_at,
            'deleted_at' => $this->deleted_at,
            'is_edited' => $this->is_edited,
            'is_deleted' => $this->is_deleted,
            'is_pinned' => $this->is_pinned,
            'accessibility_data' => $this->accessibility_data,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'profile_photo' => $this->user->profile_photo,
                ];
            }),
            'conversation' => $this->whenLoaded('conversation', function () {
                return [
                    'id' => $this->conversation->id,
                    'title' => $this->conversation->title,
                    'type' => $this->conversation->type,
                    'status' => $this->conversation->status,
                ];
            }),
            'parent' => $this->whenLoaded('parent', function () {
                return [
                    'id' => $this->parent->id,
                    'content' => $this->parent->content,
                    'type' => $this->parent->type,
                ];
            }),
            'reactions_count' => $this->whenLoaded('reactions', function () {
                return $this->reactions->count();
            }),
            'reads_count' => $this->whenLoaded('reads', function () {
                return $this->reads->count();
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
        ];
    }
}
