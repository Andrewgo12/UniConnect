<?php

namespace App\Services;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UserService
{
    public function getProfile(User $user): User
    {
        return $user->load('profile');
    }

    public function updateProfile(User $user, array $data): User
    {
        $this->validateProfileData($data);

        if (isset($data['name'])) {
            $user->update(['name' => $data['name']]);
        }

        if (array_key_exists('phone', $data)) {
            $user->update(['phone' => $data['phone']]);
        }

        if (array_key_exists('accessibility_needs', $data)) {
            $user->update([
                'accessibility_needs' => is_array($data['accessibility_needs'])
                    ? $data['accessibility_needs']
                    : [],
            ]);
        }

        $profileData = $data['profile'] ?? [];
        $profile = $user->profile ?? Profile::firstOrNew(
            ['user_id' => $user->id],
            [
                'name' => $user->name,
                'blind' => false,
                'deaf' => false,
                'mute' => false,
                'preferences' => [],
            ]
        );

        $profile->name = $user->name;
        $profile->blind = $profileData['blind'] ?? $profile->blind;
        $profile->deaf = $profileData['deaf'] ?? $profile->deaf;
        $profile->mute = $profileData['mute'] ?? $profile->mute;
        $profile->preferences = $profileData['preferences'] ?? $profile->preferences;
        $profile->save();

        return $user->load('profile');
    }

    private function validateProfileData(array $data): void
    {
        $validator = Validator::make($data, [
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'accessibility_needs' => 'nullable|array',
            'accessibility_needs.*' => 'string',
            'profile' => 'nullable|array',
            'profile.blind' => 'boolean',
            'profile.deaf' => 'boolean',
            'profile.mute' => 'boolean',
            'profile.preferences' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
