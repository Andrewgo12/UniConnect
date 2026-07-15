<?php

namespace App\Policies;

use App\Models\AccessibilityLog;
use App\Models\User;

class AccessibilityLogPolicy
{
    public function view(User $user, AccessibilityLog $accessibilityLog): bool
    {
        return $accessibilityLog->user_id === $user->id;
    }
}
