<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Models\Analytics;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class UserRegisteredListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(UserRegistered $event): void
    {
        try {
            $user = $event->user;

            Log::info('User registered', ['user_id' => $user->id, 'email' => $user->email]);

            Analytics::trackEvent([
                'user_id'       => $user->id,
                'event_type'    => 'user_action',
                'category'      => 'engagement',
                'action'        => 'user_registered',
                'resource_type' => 'user',
                'resource_id'   => $user->id,
                'value'         => 1,
            ]);

            if (!$user->profile) {
                $user->profile()->create([
                    'name'        => $user->name,
                    'blind'       => false,
                    'deaf'        => false,
                    'mute'        => false,
                    'preferences' => [],
                ]);
            }
        } catch (\Exception $e) {
            Log::error('UserRegisteredListener failed', ['error' => $e->getMessage()]);
        }
    }
}
