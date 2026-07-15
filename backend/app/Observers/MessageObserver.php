<?php

namespace App\Observers;

use App\Models\Message;
use Illuminate\Support\Facades\Log;

class MessageObserver
{
    public function created(Message $message): void
    {
        Log::info('Message created', ['message_id' => $message->id, 'type' => $message->type]);
    }

    public function updated(Message $message): void
    {
        if ($message->isDirty('content')) {
            $message->is_edited = true;
            $message->edited_at = now();
            $message->saveQuietly();
        }
    }

    public function deleted(Message $message): void
    {
        Log::info('Message deleted', ['message_id' => $message->id]);
    }
}
