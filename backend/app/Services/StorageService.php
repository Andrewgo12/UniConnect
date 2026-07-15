<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class StorageService
{
    public function storeFile(UploadedFile $file, string $directory = 'uploads', string $disk = 'public'): string
    {
        return $file->store($directory, $disk);
    }

    public function deleteFile(string $path, string $disk = 'public'): bool
    {
        return Storage::disk($disk)->exists($path) ? Storage::disk($disk)->delete($path) : false;
    }

    public function getFileUrl(string $path, string $disk = 'public'): ?string
    {
        return Storage::disk($disk)->exists($path) ? Storage::disk($disk)->url($path) : null;
    }

    public function storeImage(UploadedFile $file, string $directory = 'images', string $disk = 'public'): string
    {
        return $this->storeFile($file, $directory, $disk);
    }

    public function storeAudio(UploadedFile $file, string $directory = 'audio', string $disk = 'public'): string
    {
        return $this->storeFile($file, $directory, $disk);
    }
}
