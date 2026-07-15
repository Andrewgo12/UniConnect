<?php

namespace App\Policies;

use App\Models\Audio;
use App\Models\User;

class AudioPolicy
{
    public function view(User $user, Audio $audio): bool
    {
        return $audio->user_id === $user->id || $audio->is_public;
    }

    public function update(User $user, Audio $audio): bool
    {
        return $audio->user_id === $user->id;
    }

    public function delete(User $user, Audio $audio): bool
    {
        return $audio->user_id === $user->id;
    }
}
