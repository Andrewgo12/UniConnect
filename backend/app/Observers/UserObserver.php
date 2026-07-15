<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    public function created(User $user): void
    {
        Log::info('User created', ['user_id' => $user->id, 'email' => $user->email]);
    }

    public function updated(User $user): void
    {
        Log::info('User updated', ['user_id' => $user->id, 'changed' => array_keys($user->getDirty())]);
    }

    public function deleted(User $user): void
    {
        Log::info('User deleted', ['user_id' => $user->id]);
    }
}
