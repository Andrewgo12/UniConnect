<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ConversationService
{
    public function listForUser(int $userId)
    {
        return Conversation::whereHas('participants', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->with(['lastMessage', 'participants'])
        ->latest('updated_at')
        ->get();
    }

    public function create(array $data, User $user): Conversation
    {
        $this->validateConversationData($data);

        $conversation = Conversation::create([
            'created_by' => $user->id,
            'title' => $data['title'],
            'type' => $data['type'] ?? 'text',
            'metadata' => $data['metadata'] ?? [],
            'settings' => $data['settings'] ?? [],
            'status' => 'active',
        ]);

        $participants = collect($data['participant_ids'] ?? [])
            ->push($user->id)
            ->unique()
            ->toArray();

        $conversation->participants()->sync($participants);

        return $conversation->load('participants');
    }

    public function find(Conversation $conversation): Conversation
    {
        return $conversation->load(['participants', 'messages' => function ($query) {
            $query->latest()->limit(50);
        }]);
    }

    public function update(Conversation $conversation, array $data): Conversation
    {
        $this->validateConversationData($data, false);
        $conversation->update($data);
        return $conversation->load('participants');
    }

    public function delete(Conversation $conversation): void
    {
        $conversation->delete();
    }

    public function addParticipant(Conversation $conversation, int $userId): Conversation
    {
        $conversation->participants()->syncWithoutDetaching([$userId]);
        return $conversation->load('participants');
    }

    public function removeParticipant(Conversation $conversation, int $userId): Conversation
    {
        $conversation->participants()->detach($userId);
        return $conversation->load('participants');
    }

    public function markAsRead(Conversation $conversation, int $userId): void
    {
        $conversation->participants()->updateExistingPivot($userId, [
            'last_read_at' => now(),
        ]);
    }

    private function validateConversationData(array $data, bool $required = true): void
    {
        $rules = [
            'title' => ($required ? 'required|' : '') . 'string|max:255',
            'type' => 'nullable|string|in:text,voice,sign_language',
            'participant_ids' => 'nullable|array',
            'participant_ids.*' => 'exists:users,id',
            'metadata' => 'nullable|array',
            'settings' => 'nullable|array',
        ];

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
