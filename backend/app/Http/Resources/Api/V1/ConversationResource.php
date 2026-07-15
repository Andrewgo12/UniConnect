<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
            'status' => $this->status,
            'priority' => $this->priority,
            'category' => $this->category,
            'settings' => $this->settings,
            'metadata' => $this->metadata,
            'created_by' => $this->created_by,
            'closed_by' => $this->closed_by,
            'closed_at' => $this->closed_at,
            'is_public' => $this->is_public,
            'is_pinned' => $this->is_pinned,
            'is_muted' => $this->is_muted,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'creator' => $this->whenLoaded('creator', function () {
                return [
                    'id' => $this->creator->id,
                    'name' => $this->creator->name,
                    'profile_photo' => $this->creator->profile_photo,
                ];
            }),
            'participants' => $this->whenLoaded('participants', function () {
                return $this->participants->map(function ($participant) {
                    return [
                        'id' => $participant->id,
                        'user_id' => $participant->user_id,
                        'role' => $participant->role,
                        'permissions' => $participant->permissions,
                        'joined_at' => $participant->joined_at,
                        'left_at' => $participant->left_at,
                    ];
                });
            }),
            'last_message' => $this->whenLoaded('lastMessage', function () {
                return [
                    'id' => $this->lastMessage->id,
                    'content' => $this->lastMessage->content,
                    'type' => $this->lastMessage->type,
                    'created_at' => $this->lastMessage->created_at,
                ];
            }),
            'messages_count' => $this->whenLoaded('messages', function () {
                return $this->messages->count();
            }),
            'unread_count' => $this->whenLoaded('unreadMessages', function () {
                return $this->unreadMessages->count();
            }),
        ];
    }
}
