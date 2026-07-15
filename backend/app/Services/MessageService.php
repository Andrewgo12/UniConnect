<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Phrase;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class MessageService
{
    public function listForUser(int $userId)
    {
        return Message::where('user_id', $userId)
            ->with(['user', 'conversation'])
            ->latest()
            ->get();
    }

    public function create(array $data, int $userId): Message
    {
        $this->validateMessageData($data);

        return Message::create([
            'user_id' => $userId,
            'content' => $data['content'],
            'type' => $data['type'] ?? 'text',
            'conversation_id' => $data['conversation_id'] ?? null,
            'metadata' => $data['metadata'] ?? [],
            'accessibility_data' => $data['accessibility_data'] ?? [],
            'priority' => $data['priority'] ?? 'medium',
            'status' => $data['status'] ?? 'sent',
        ]);
    }

    public function find(Message $message): Message
    {
        return $message->load(['user', 'conversation']);
    }

    public function update(Message $message, array $data): Message
    {
        $this->validateMessageData($data, false);
        $message->update($data);
        return $message;
    }

    public function delete(Message $message): void
    {
        $message->delete();
    }

    public function getConversationMessages(Conversation $conversation)
    {
        return Message::where('conversation_id', $conversation->id)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function sendPhrase(array $data, int $userId): Message
    {
        $validator = Validator::make($data, [
            'phrase_id' => 'required|exists:phrases,id',
            'conversation_id' => 'nullable|exists:conversations,id',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $phrase = Phrase::findOrFail($data['phrase_id']);

        return Message::create([
            'user_id' => $userId,
            'content' => $phrase->text,
            'type' => 'phrase',
            'conversation_id' => $data['conversation_id'] ?? null,
            'vibration_pattern' => $phrase->vibration_pattern,
            'metadata' => [
                'phrase_id' => $phrase->id,
                'icon' => $phrase->icon,
            ],
            'status' => 'sent',
            'priority' => $phrase->priority ?? 'medium',
        ]);
    }

    private function validateMessageData(array $data, bool $required = true): void
    {
        $rules = [
            'content' => ($required ? 'required|' : '') . 'string|max:1000',
            'type' => 'nullable|string|in:text,phrase,emergency',
            'conversation_id' => 'nullable|exists:conversations,id',
            'vibration_pattern' => 'nullable|array',
            'vibration_pattern.*' => 'integer',
            'metadata' => 'nullable|array',
            'accessibility_data' => 'nullable|array',
            'priority' => 'nullable|string|in:low,medium,high,urgent',
            'status' => 'nullable|string|in:sent,delivered,read,failed',
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
