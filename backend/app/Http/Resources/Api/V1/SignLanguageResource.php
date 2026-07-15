<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SignLanguageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'title'            => $this->title,
            'description'      => $this->description,
            'category'         => $this->category,
            'difficulty_level' => $this->difficulty_level,
            'region'           => $this->region,
            'video_url'        => $this->video_url,
            'image_url'        => $this->image_url,
            'thumbnail_url'    => $this->thumbnail_url,
            'duration'         => $this->duration,
            'tags'             => $this->tags,
            'is_public'        => $this->is_public,
            'is_approved'      => $this->is_approved,
            'usage_count'      => $this->usage_count,
            'metadata'         => $this->metadata,
            'created_at'       => $this->created_at,
            'updated_at'       => $this->updated_at,
            'user'             => $this->whenLoaded('user', fn() => [
                'id'   => $this->user->id,
                'name' => $this->user->name,
            ]),
        ];
    }
}
