<?php

namespace App\Services;

use App\Models\Phrase;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PhraseService
{
    public function list(array $filters = [])
    {
        $query = Phrase::active()->ordered();

        if (!empty($filters['category'])) {
            $query->byCategory($filters['category']);
        }

        return $query->get();
    }

    public function create(array $data): Phrase
    {
        $this->validatePhraseData($data, true);

        return Phrase::create([
            'text' => $data['text'],
            'icon' => $data['icon'] ?? null,
            'vibration_pattern' => $data['vibration_pattern'] ?? [],
            'category' => $data['category'] ?? 'general',
            'order' => $data['order'] ?? 0,
            'active' => $data['active'] ?? true,
        ]);
    }

    public function find(Phrase $phrase): Phrase
    {
        return $phrase;
    }

    public function update(Phrase $phrase, array $data): Phrase
    {
        $this->validatePhraseData($data, false);
        $phrase->update($data);
        return $phrase;
    }

    public function delete(Phrase $phrase): void
    {
        $phrase->delete();
    }

    public function defaults(): array
    {
        return [
            [
                'id' => 1,
                'text' => 'Sí',
                'icon' => '✓',
                'vibration_pattern' => [100],
                'category' => 'general',
                'order' => 1,
            ],
            [
                'id' => 2,
                'text' => 'No',
                'icon' => '✗',
                'vibration_pattern' => [100, 80, 100],
                'category' => 'general',
                'order' => 2,
            ],
            [
                'id' => 3,
                'text' => 'Ayuda',
                'icon' => '!',
                'vibration_pattern' => [200, 100, 200, 100, 200],
                'category' => 'emergency',
                'order' => 3,
            ],
            [
                'id' => 4,
                'text' => 'Gracias',
                'icon' => '♥',
                'vibration_pattern' => [50, 50, 50],
                'category' => 'general',
                'order' => 4,
            ],
            [
                'id' => 5,
                'text' => 'Agua',
                'icon' => '💧',
                'vibration_pattern' => [150, 80, 150],
                'category' => 'medical',
                'order' => 5,
            ],
            [
                'id' => 6,
                'text' => 'Baño',
                'icon' => '🚽',
                'vibration_pattern' => [100, 100, 100, 100],
                'category' => 'general',
                'order' => 6,
            ],
            [
                'id' => 7,
                'text' => 'Dolor',
                'icon' => '⚠',
                'vibration_pattern' => [300, 100, 300],
                'category' => 'medical',
                'order' => 7,
            ],
            [
                'id' => 8,
                'text' => 'Llamar',
                'icon' => '📞',
                'vibration_pattern' => [400],
                'category' => 'emergency',
                'order' => 8,
            ],
        ];
    }

    private function validatePhraseData(array $data, bool $required = true): void
    {
        $validator = Validator::make($data, [
            'text' => ($required ? 'required|' : '') . 'string|max:255',
            'icon' => 'nullable|string|max:50',
            'vibration_pattern' => ($required ? 'required|' : '') . 'array',
            'vibration_pattern.*' => 'integer',
            'category' => 'nullable|string|in:general,emergency,medical',
            'order' => 'nullable|integer',
            'active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
