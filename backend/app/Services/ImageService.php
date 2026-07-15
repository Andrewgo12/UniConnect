<?php

namespace App\Services;

use App\Models\Image;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ImageService
{
    public function listForUser(int $userId)
    {
        return Image::where('user_id', $userId)
            ->latest()
            ->get();
    }

    public function create(array $data, int $userId, $file): Image
    {
        $this->validateImageData($data, true);

        $path = $file->store('images', 'public');

        return Image::create([
            'user_id' => $userId,
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'type' => $data['type'] ?? 'profile',
            'description' => $data['description'] ?? null,
            'metadata' => $data['metadata'] ?? [],
        ]);
    }

    public function find(Image $image): Image
    {
        return $image;
    }

    public function update(Image $image, array $data): Image
    {
        $this->validateImageData($data, false);
        $image->update($data);
        return $image;
    }

    public function delete(Image $image): void
    {
        $image->delete();
    }

    public function listByType(int $userId, string $type)
    {
        return Image::where('user_id', $userId)
            ->where('type', $type)
            ->latest()
            ->get();
    }

    public function getProfileImage(int $userId)
    {
        return Image::where('user_id', $userId)
            ->where('type', 'profile')
            ->latest()
            ->first();
    }

    public function uploadProfileImage(int $userId, $file): Image
    {
        $path = $file->store('images', 'public');

        Image::where('user_id', $userId)
            ->where('type', 'profile')
            ->delete();

        return Image::create([
            'user_id' => $userId,
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'type' => 'profile',
        ]);
    }

    private function validateImageData(array $data, bool $required = true): void
    {
        $rules = [
            'type' => ($required ? 'required|' : '') . 'string|in:profile,sign_language,emergency,medical',
            'description' => 'nullable|string|max:500',
            'metadata' => 'nullable|array',
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
