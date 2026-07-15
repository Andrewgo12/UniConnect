<?php

namespace App\Services;

use App\Models\Emergency;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class EmergencyService
{
    private const TYPES = 'medical,security,help,accident,violence,natural_disaster,technical,other';

    public function listForUser(int $userId)
    {
        return Emergency::where('user_id', $userId)
            ->with('user')
            ->latest()
            ->get();
    }

    public function create(array $data, User $user): Emergency
    {
        $this->validateEmergencyData($data);

        return Emergency::create($this->payloadFromInput($data, $user->id));
    }

    public function find(Emergency $emergency): Emergency
    {
        return $emergency->load('user');
    }

    public function update(Emergency $emergency, array $data): Emergency
    {
        $this->validateEmergencyData($data, false);

        if (isset($data['status']) && $data['status'] === 'resolved') {
            $data['resolved_at'] = now();
        }

        if (isset($data['status']) && $data['status'] === 'acknowledged' && empty($data['acknowledged_at'])) {
            $data['acknowledged_at'] = now();
        }

        $emergency->update($data);

        return $emergency->fresh()->load('user');
    }

    public function delete(Emergency $emergency): void
    {
        $emergency->delete();
    }

    public function trigger(array $data, User $user): Emergency
    {
        $this->validateEmergencyData($data);

        $emergency = Emergency::create($this->payloadFromInput($data, $user->id));

        NotificationService::sendEmergencyNotification($user->id, $emergency);

        return $emergency;
    }

    public function activeForUser(int $userId)
    {
        return Emergency::where('user_id', $userId)
            ->where('status', 'active')
            ->with('user')
            ->latest()
            ->get();
    }

    /**
     * @return array<string, mixed>
     */
    private function payloadFromInput(array $data, int $userId): array
    {
        return [
            'user_id' => $userId,
            'type' => $data['type'],
            'description' => $data['description'] ?? null,
            'location' => $this->normalizeLocation($data['location'] ?? null),
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'status' => $data['status'] ?? 'active',
            'severity' => $data['severity'] ?? null,
            'contact_name' => $data['contact_name'] ?? null,
            'contact_phone' => $data['contact_phone'] ?? null,
            'contact_relationship' => $data['contact_relationship'] ?? null,
            'medical_conditions' => $data['medical_conditions'] ?? null,
            'accessibility_needs' => $data['accessibility_needs'] ?? null,
            'metadata' => $data['metadata'] ?? [],
        ];
    }

    private function normalizeLocation(mixed $location): ?string
    {
        if ($location === null || $location === '') {
            return null;
        }

        if (is_array($location)) {
            return json_encode($location);
        }

        return (string) $location;
    }

    private function validateEmergencyData(array $data, bool $required = true): void
    {
        $typeRule = ($required ? 'required|' : 'sometimes|').'string|in:'.self::TYPES;

        $rules = [
            'type' => $typeRule,
            'description' => 'nullable|string',
            'location' => 'nullable',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'status' => 'nullable|string|in:active,acknowledged,resolved,cancelled',
            'severity' => 'nullable|string|in:low,medium,high,critical',
            'contact_name' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'contact_relationship' => 'nullable|string|max:100',
            'medical_conditions' => 'nullable|array',
            'medical_conditions.*' => 'string',
            'accessibility_needs' => 'nullable|array',
            'accessibility_needs.*' => 'string',
            'metadata' => 'nullable|array',
            'acknowledged_at' => 'nullable|date',
        ];

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        if (isset($data['location']) && ! is_array($data['location']) && ! is_string($data['location'])) {
            throw ValidationException::withMessages([
                'location' => ['Location must be a string or an array.'],
            ]);
        }

        if (is_array($data['location'] ?? null)) {
            $nested = Validator::make($data['location'], [
                'lat' => 'nullable|numeric',
                'lng' => 'nullable|numeric',
            ]);
            if ($nested->fails()) {
                throw new ValidationException($nested);
            }
        }
    }
}
