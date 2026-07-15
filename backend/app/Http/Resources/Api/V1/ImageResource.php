<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ImageResource extends JsonResource
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
            'file_path' => $this->file_path,
            'original_name' => $this->original_name,
            'mime_type' => $this->mime_type,
            'size' => $this->size,
            'width' => $this->width,
            'height' => $this->height,
            'alt_text' => $this->alt_text,
            'tags' => $this->tags,
            'is_public' => $this->is_public,
            'is_approved' => $this->is_approved,
            'usage_count' => $this->usage_count,
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
                return [
                    'image' => $this->getFirstMedia('images') ? [
                        'id' => $this->getFirstMedia('images')->id,
                        'name' => $this->getFirstMedia('images')->file_name,
                        'mime_type' => $this->getFirstMedia('images')->mime_type,
                        'size' => $this->getFirstMedia('images')->size,
                        'url' => $this->getFirstMedia('images')->getUrl(),
                        'thumbnail_url' => $this->getFirstMedia('thumbnails')?->getUrl(),
                    ] : null,
                ];
            }),
            'favorites_count' => $this->whenLoaded('favorites', function () {
                return $this->favorites->count();
            }),
            'views_count' => $this->whenLoaded('views', function () {
                return $this->views->count();
            }),
            'aspect_ratio' => $this->when(isset($this->width) && isset($this->height), function () {
                return round($this->width / $this->height, 2);
            }),
            'formatted_size' => $this->when(isset($this->size), function () {
                $bytes = $this->size;
                if ($bytes >= 1073741824) {
                    return number_format($bytes / 1073741824, 2) . ' GB';
                } elseif ($bytes >= 1048576) {
                    return number_format($bytes / 1048576, 2) . ' MB';
                } elseif ($bytes >= 1024) {
                    return number_format($bytes / 1024, 2) . ' KB';
                } else {
                    return $bytes . ' bytes';
                }
            }),
        ];
    }
}
