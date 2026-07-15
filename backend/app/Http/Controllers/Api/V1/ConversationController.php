<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Services\ConversationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ConversationController extends Controller
{
    protected ConversationService $conversationService;

    public function __construct(ConversationService $conversationService)
    {
        $this->conversationService = $conversationService;
    }

    /**
     * Display a listing of conversations.
     */
    public function index(Request $request)
    {
        $conversations = $this->conversationService->listForUser($request->user()->id);
        return response()->json($conversations);
    }

    /**
     * Store a newly created conversation.
     */
    public function store(Request $request)
    {
        try {
            $conversation = $this->conversationService->create($request->all(), $request->user());
            return response()->json($conversation, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * Display the specified conversation.
     */
    public function show(Conversation $conversation)
    {
        $this->authorize('view', $conversation);
        return response()->json($this->conversationService->find($conversation));
    }

    /**
     * Update the specified conversation.
     */
    public function update(Request $request, Conversation $conversation)
    {
        $this->authorize('update', $conversation);

        try {
            $updated = $this->conversationService->update($conversation, $request->all());
            return response()->json($updated);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * Remove the specified conversation.
     */
    public function destroy(Conversation $conversation)
    {
        $this->authorize('delete', $conversation);
        $this->conversationService->delete($conversation);
        return response()->json(null, 204);
    }

    /**
     * Add participant to conversation
     */
    public function addParticipant(Request $request, Conversation $conversation)
    {
        $this->authorize('update', $conversation);

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $conversation = $this->conversationService->addParticipant($conversation, $request->user_id);

        return response()->json($conversation);
    }

    /**
     * Remove participant from conversation
     */
    public function removeParticipant(Request $request, Conversation $conversation)
    {
        $this->authorize('update', $conversation);

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $conversation = $this->conversationService->removeParticipant($conversation, $request->user_id);

        return response()->json($conversation);
    }

    /**
     * Mark conversation as read
     */
    public function markAsRead(Request $request, Conversation $conversation)
    {
        $this->authorize('view', $conversation);

        $this->conversationService->markAsRead($conversation, $request->user()->id);

        return response()->json(['message' => 'Conversation marked as read']);
    }
}
