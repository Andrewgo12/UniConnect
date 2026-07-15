<?php

namespace App\Policies;

use App\Models\SignLanguage;
use App\Models\User;

class SignLanguagePolicy
{
    public function view(User $user, SignLanguage $signLanguage): bool
    {
        return $signLanguage->user_id === $user->id || $signLanguage->is_public;
    }

    public function update(User $user, SignLanguage $signLanguage): bool
    {
        return $signLanguage->user_id === $user->id;
    }

    public function delete(User $user, SignLanguage $signLanguage): bool
    {
        return $signLanguage->user_id === $user->id;
    }
}
