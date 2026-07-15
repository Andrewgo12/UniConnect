<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AudioResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'title'          => $this->title,
            'description'    => $this->description,
            'type'           => $this->type,
            'file_path'      => $this->file_path,
            'original_name'  => $this->original_name,
            'mime_type'      => $this->mime_type,
            'size'           => $this->size,
            'duration'       => $this->duration,
            'transcript'     => $this->transcript,
            'language'       => $this->language,
            'quality'        => $this->quality,
            'is_public'      => $this->is_public,
            'is_processed'   => $this->is_processed,
            'metadata'       => $this->metadata,
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,
            'user'           => $this->whenLoaded('user', fn() => [
                'id'   => $this->user->id,
                'name' => $this->user->name,
            ]),
        ];
    }
}
