<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\MessageService;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    protected MessageService $messageService;

    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    /**
     * Display a listing of messages.
     */
    public function index(Request $request)
    {
        $messages = $this->messageService->listForUser($request->user()->id);
        return response()->json($messages);
    }

    /**
     * Store a newly created message.
     */
    public function store(Request $request)
    {
        try {
            $message = $this->messageService->create($request->all(), $request->user()->id);
            return response()->json($message, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * Display the specified message.
     */
    public function show(Message $message)
    {
        $this->authorize('view', $message);
        return response()->json($this->messageService->find($message));
    }

    /**
     * Update the specified message in storage.
     */
    public function update(Request $request, Message $message)
    {
        $this->authorize('update', $message);

        try {
            $updated = $this->messageService->update($message, $request->all());
            return response()->json($updated);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * Remove the specified message from storage.
     */
    public function destroy(Message $message)
    {
        $this->authorize('delete', $message);
        $this->messageService->delete($message);
        return response()->json(null, 204);
    }

    /**
     * Get messages for a conversation
     */
    public function conversation(Request $request, Conversation $conversation)
    {
        $this->authorize('view', $conversation);
        $messages = $this->messageService->getConversationMessages($conversation);
        return response()->json($messages);
    }

    /**
     * Send phrase message
     */
    public function sendPhrase(Request $request)
    {
        try {
            $message = $this->messageService->sendPhrase($request->all(), $request->user()->id);
            return response()->json($message, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }
}
