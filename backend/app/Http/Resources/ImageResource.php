<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ImageResource extends JsonResource
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
            'width' => $this->width,
            'height' => $this->height,
            'alt_text' => $this->alt_text,
            'aspect_ratio' => $this->getAspectRatio(),
            'formatted_size' => $this->getFormattedSize(),
            'url' => $this->file_path,
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
