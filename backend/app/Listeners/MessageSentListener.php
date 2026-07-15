<?php

namespace App\Listeners;

use App\Events\MessageSent;
use App\Models\Analytics;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class MessageSentListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(MessageSent $event): void
    {
        try {
            $message = $event->message;
            $user    = $event->user;

            Log::info('Message sent', [
                'message_id'      => $message->id,
                'user_id'         => $user->id,
                'conversation_id' => $message->conversation_id,
            ]);

            Analytics::trackEvent([
                'user_id'       => $user->id,
                'event_type'    => 'user_action',
                'category'      => 'engagement',
                'action'        => 'message_sent',
                'resource_type' => 'message',
                'resource_id'   => $message->id,
                'value'         => 1,
                'metadata'      => ['message_type' => $message->type],
            ]);
        } catch (\Exception $e) {
            Log::error('MessageSentListener failed', ['error' => $e->getMessage()]);
        }
    }
}
