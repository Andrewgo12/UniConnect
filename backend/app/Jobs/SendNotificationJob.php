<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Message;
use App\Models\Emergency;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [60, 300, 900];
    public $timeout = 120;

    /**
     * Create a new job instance.
     *
     * @param User $user
     * @param string $type
     * @param array $data
     */
    public function __construct(
        public User $user,
        public string $type,
        public array $data = []
    ) {
        $this->user = $user;
        $this->type = $type;
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            switch ($this->type) {
                case 'message':
                    $this->sendMessageNotification();
                    break;
                case 'emergency':
                    $this->sendEmergencyNotification();
                    break;
                case 'system':
                    $this->sendSystemNotification();
                    break;
                case 'accessibility':
                    $this->sendAccessibilityNotification();
                    break;
                default:
                    $this->sendGenericNotification();
                    break;
            }

        } catch (\Exception $e) {
            Log::error('Notification sending failed: ' . $e->getMessage(), [
                'user_id' => $this->user->id,
                'type' => $this->type,
                'data' => $this->data,
            ]);
            
            throw $e;
        }
    }

    /**
     * Send message notification.
     */
    private function sendMessageNotification(): void
    {
        $message = $this->data['message'] ?? null;
        $conversationId = $this->data['conversation_id'] ?? null;

        Log::info('Message notification sent', [
            'user_id' => $this->user->id,
            'message_id' => $message->id ?? null,
            'conversation_id' => $conversationId,
        ]);

        // Here you would integrate with actual notification service
        // $this->user->notify(new MessageNotification($message));
    }

    /**
     * Send emergency notification.
     */
    private function sendEmergencyNotification(): void
    {
        $emergency = $this->data['emergency'] ?? null;

        Log::info('Emergency notification sent', [
            'user_id' => $this->user->id,
            'emergency_id' => $emergency->id ?? null,
            'emergency_type' => $emergency->type ?? null,
        ]);

        // Here you would integrate with actual notification service
        // $this->user->notify(new EmergencyNotification($emergency));
    }

    /**
     * Send system notification.
     */
    private function sendSystemNotification(): void
    {
        $title = $this->data['title'] ?? 'Notificación del Sistema';
        $content = $this->data['content'] ?? 'Tienes una nueva notificación';

        Log::info('System notification sent', [
            'user_id' => $this->user->id,
            'title' => $title,
            'content' => $content,
        ]);

        // Here you would integrate with actual notification service
        // $this->user->notify(new SystemNotification($title, $content));
    }

    /**
     * Send accessibility notification.
     */
    private function sendAccessibilityNotification(): void
    {
        $feature = $this->data['feature'] ?? 'unknown';
        $message = $this->data['message'] ?? 'Se ha actualizado una configuración de accesibilidad';

        Log::info('Accessibility notification sent', [
            'user_id' => $this->user->id,
            'feature' => $feature,
            'message' => $message,
        ]);

        // Here you would integrate with actual notification service
        // $this->user->notify(new AccessibilityNotification($feature, $message));
    }

    /**
     * Send generic notification.
     */
    private function sendGenericNotification(): void
    {
        $title = $this->data['title'] ?? 'Notificación';
        $content = $this->data['content'] ?? 'Tienes una nueva notificación';

        Log::info('Generic notification sent', [
            'user_id' => $this->user->id,
            'title' => $title,
            'content' => $content,
        ]);

        // Here you would integrate with actual notification service
        // $this->user->notify(new GenericNotification($title, $content));
    }

    /**
     * The job failed to process.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Notification job failed', [
            'user_id' => $this->user->id,
            'type' => $this->type,
            'exception' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }

    /**
     * The job has finished processing.
     */
    public function success(): void
    {
        Log::info('Notification job completed successfully', [
            'user_id' => $this->user->id,
            'type' => $this->type,
            'completed_at' => now(),
        ]);
    }
}
