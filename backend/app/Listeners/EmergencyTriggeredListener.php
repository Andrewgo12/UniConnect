<?php

namespace App\Listeners;

use App\Events\EmergencyTriggered;
use App\Models\Analytics;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class EmergencyTriggeredListener
{
    use InteractsWithQueue;

    public function handle(EmergencyTriggered $event): void
    {
        try {
            $emergency = $event->emergency;
            $user      = $event->user;

            Log::warning('Emergency triggered', [
                'emergency_id' => $emergency->id,
                'type'         => $emergency->type,
                'user_id'      => $user->id,
            ]);

            Analytics::trackEvent([
                'user_id'       => $user->id,
                'event_type'    => 'emergency_event',
                'category'      => 'emergency',
                'action'        => 'emergency_triggered',
                'resource_type' => 'emergency',
                'resource_id'   => $emergency->id,
                'value'         => 1,
                'metadata'      => [
                    'emergency_type' => $emergency->type,
                    'location'       => $emergency->location,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('EmergencyTriggeredListener failed', ['error' => $e->getMessage()]);
        }
    }
}
