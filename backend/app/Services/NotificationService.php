<?php

namespace App\Services;

use App\Models\Emergency;
use App\Models\Message;
use Illuminate\Support\Facades\Log;
use App\Services\RealTimeNotificationService;

class NotificationService
{
    public static function send(array $data): bool
    {
        try {
            return RealTimeNotificationService::sendNotification($data['user_id'], $data);
        } catch (\Exception $e) {
            Log::error('NotificationService send failed', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);
            return false;
        }
    }

    public static function sendMessageNotification(int $userId, Message $message, array $options = []): bool
    {
        try {
            return RealTimeNotificationService::sendMessageNotification($userId, $message, $options);
        } catch (\Exception $e) {
            Log::error('NotificationService message notification failed', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'message_id' => $message->id,
            ]);
            return false;
        }
    }

    public static function sendEmergencyNotification(int $userId, Emergency $emergency): bool
    {
        try {
            return RealTimeNotificationService::sendEmergencyNotification($userId, $emergency);
        } catch (\Exception $e) {
            Log::error('NotificationService emergency notification failed', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'emergency_id' => $emergency->id,
            ]);
            return false;
        }
    }
}
