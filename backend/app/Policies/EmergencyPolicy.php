<?php

namespace App\Policies;

use App\Models\Emergency;
use App\Models\User;

class EmergencyPolicy
{
    public function view(User $user, Emergency $emergency): bool
    {
        return $emergency->user_id === $user->id;
    }

    public function update(User $user, Emergency $emergency): bool
    {
        return $emergency->user_id === $user->id;
    }

    public function delete(User $user, Emergency $emergency): bool
    {
        return $emergency->user_id === $user->id;
    }
}
