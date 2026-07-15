<?php

namespace App\Services;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Register a new user with profile
     */
    public function register(array $data): User
    {
        $this->validateRegistrationData($data);

        $needs = $data['accessibility_needs'] ?? [];
        $needs = is_array($needs) ? $needs : [];

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'accessibility_needs' => $needs,
        ]);

        // Create profile with accessibility settings
        $profileData = $data['profile'] ?? [];
        Profile::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'blind' => $profileData['blind'] ?? in_array('blind', $needs, true),
            'deaf' => $profileData['deaf'] ?? in_array('deaf', $needs, true),
            'mute' => $profileData['mute'] ?? in_array('mute', $needs, true),
            'preferences' => $profileData['preferences'] ?? [],
        ]);

        return $user;
    }

    /**
     * Authenticate user and return token
     */
    public function login(array $data): array
    {
        $this->validateLoginData($data);

        if (!Auth::attempt($data)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
                'password' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = Auth::user();
        $token = $user->createToken('api-token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
            'expires_at' => null,
        ];
    }

    /**
     * Logout user by revoking token
     */
    public function logout(User $user): void
    {
        $user->tokens()->delete();
    }

    /**
     * Get authenticated user with profile
     */
    public function getAuthenticatedUser(): User
    {
        return Auth::user()->load('profile');
    }

    /**
     * Validate registration data
     */
    private function validateRegistrationData(array $data): void
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'terms_accepted' => 'sometimes|accepted',
            'privacy_accepted' => 'sometimes|accepted',
            'accessibility_needs' => 'nullable|array',
            'accessibility_needs.*' => 'string',
            'profile' => 'nullable|array',
            'profile.blind' => 'boolean',
            'profile.deaf' => 'boolean',
            'profile.mute' => 'boolean',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    /**
     * Validate login data
     */
    private function validateLoginData(array $data): void
    {
        $validator = Validator::make($data, [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
