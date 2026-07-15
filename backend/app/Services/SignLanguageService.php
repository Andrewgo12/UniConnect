<?php

namespace App\Services;

use App\Models\SignLanguage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class SignLanguageService
{
    public function listForUser(int $userId)
    {
        return SignLanguage::where('user_id', $userId)
            ->latest()
            ->get();
    }

    public function create(array $data, int $userId)
    {
        $this->validateSignLanguageData($data, true);

        return SignLanguage::create([
            'user_id' => $userId,
            'title' => $data['name'] ?? $data['title'],
            'category' => $data['category'] ?? 'custom',
            'description' => $data['description'] ?? null,
            'video_url' => $data['video_url'] ?? null,
            'image_url' => $data['image_url'] ?? null,
            'is_approved' => $data['is_active'] ?? $data['is_approved'] ?? false,
            'is_public' => $data['is_public'] ?? true,
            'metadata' => $data['metadata'] ?? [],
        ]);
    }

    public function find(SignLanguage $signLanguage)
    {
        return $signLanguage;
    }

    public function update(SignLanguage $signLanguage, array $data)
    {
        $this->validateSignLanguageData($data, false);
        $signLanguage->update($data);
        return $signLanguage;
    }

    public function delete(SignLanguage $signLanguage)
    {
        $signLanguage->delete();
    }

    public function getCategories(): array
    {
        return [
            ['value' => 'colombian', 'label' => 'Colombiano'],
            ['value' => 'international', 'label' => 'Internacional'],
            ['value' => 'asl', 'label' => 'ASL'],
            ['value' => 'custom', 'label' => 'Personalizado'],
        ];
    }

    public function basicSigns(string $category = 'colombian')
    {
        return SignLanguage::where('category', $category)
            ->where('is_approved', true)
            ->get(['id', 'title', 'image_url', 'video_url']);
    }

    public function emergencySigns()
    {
        return SignLanguage::where('category', 'emergency')
            ->where('is_approved', true)
            ->get(['id', 'title', 'image_url', 'video_url']);
    }

    private function validateSignLanguageData(array $data, bool $required = true): void
    {
        $rules = [
            'name' => ($required ? 'required|' : '') . 'string|max:255',
            'category' => ($required ? 'required|' : '') . 'string|in:colombian,international,asl,custom,emergency',
            'description' => 'nullable|string|max:1000',
            'video_url' => 'nullable|url',
            'image_url' => 'nullable|url',
            'is_active' => 'boolean',
            'metadata' => 'nullable|array',
        ];

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
