<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SecurityService
{
    public function hashPassword(string $password): string
    {
        return Hash::make($password);
    }

    public function verifyPassword(string $password, string $hash): bool
    {
        return Hash::check($password, $hash);
    }

    public function generateToken(int $length = 40): string
    {
        return Str::random($length);
    }

    public function sanitizeInput(array $input): array
    {
        return array_map(function ($value) {
            if (is_string($value)) {
                return trim(strip_tags($value));
            }
            return $value;
        }, $input);
    }

    public function logSecurityEvent(array $data): void
    {
        Log::info('security_event', $data);
    }
}
