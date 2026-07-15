<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Audio;
use App\Services\AudioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AudioController extends Controller
{
    protected AudioService $audioService;

    public function __construct(AudioService $audioService)
    {
        $this->audioService = $audioService;
    }

    /**
     * Display a listing of audio files.
     */
    public function index(Request $request)
    {
        $audioFiles = $this->audioService->listForUser($request->user()->id);
        return response()->json($audioFiles);
    }

    /**
     * Store a newly created audio file.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:audio/mpeg,audio/wav,audio/ogg',
            'type' => 'string|in:speech,voice_note,emergency,sign_language',
            'transcript' => 'nullable|string',
            'duration' => 'nullable|integer',
            'metadata' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $audio = $this->audioService->create($request->all(), $request->user()->id, $request->file('file'));
        return response()->json($audio, 201);
    }

    /**
     * Display the specified audio file.
     */
    public function show(Audio $audio)
    {
        $this->authorize('view', $audio);
        return response()->json($this->audioService->find($audio));
    }

    /**
     * Update the specified audio file.
     */
    public function update(Request $request, Audio $audio)
    {
        $this->authorize('update', $audio);

        $validator = Validator::make($request->all(), [
            'type' => 'string|in:speech,voice_note,emergency,sign_language',
            'transcript' => 'nullable|string',
            'duration' => 'nullable|integer',
            'metadata' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $updated = $this->audioService->update($audio, $request->all());
        return response()->json($updated);
    }

    /**
     * Remove the specified audio file.
     */
    public function destroy(Audio $audio)
    {
        $this->authorize('delete', $audio);
        $this->audioService->delete($audio);
        return response()->json(null, 204);
    }

    /**
     * Convert speech to text
     */
    public function speechToText(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'audio_file' => 'required|file|mimes:audio/mpeg,audio/wav,audio/ogg',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $result = $this->audioService->speechToText($request->file('audio_file'), $request->user()->id);
        return response()->json($result, 201);
    }

    /**
     * Convert text to speech
     */
    public function textToSpeech(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'text' => 'required|string|max:500',
            'voice' => 'string|in:male,female,child',
            'language' => 'string|in:es,en,fr',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $result = $this->audioService->textToSpeech($request->only('text', 'voice', 'language'), $request->user()->id);
        return response()->json($result, 201);
    }
}
