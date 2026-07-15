<?php

namespace App\Listeners;

use App\Events\SecurityAlert;
use App\Models\SecurityLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SecurityAlertListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(SecurityAlert $event): void
    {
        try {
            $user      = $event->user;
            $alertType = $event->alertType;
            $message   = $event->message;

            Log::warning('Security alert', [
                'user_id'    => $user->id,
                'alert_type' => $alertType,
                'message'    => $message,
            ]);

            SecurityLog::logSecurityEvent([
                'user_id'     => $user->id,
                'event_type'  => $alertType,
                'severity'    => in_array($alertType, ['suspicious_activity', 'account_locked']) ? 'high' : 'medium',
                'description' => $message,
                'success'     => false,
                'metadata'    => $event->metadata,
            ]);
        } catch (\Exception $e) {
            Log::error('SecurityAlertListener failed', ['error' => $e->getMessage()]);
        }
    }
}
