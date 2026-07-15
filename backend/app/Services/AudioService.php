<?php

namespace App\Services;

use App\Models\Audio;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class AudioService
{
    public function listForUser(int $userId)
    {
        return Audio::where('user_id', $userId)
            ->latest()
            ->get();
    }

    public function create(array $data, int $userId, $file): Audio
    {
        $this->validateAudioData($data, true);

        $path = $file->store('audio', 'public');

        return Audio::create([
            'user_id' => $userId,
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'type' => $data['type'] ?? 'speech',
            'transcript' => $data['transcript'] ?? null,
            'duration' => $data['duration'] ?? null,
            'metadata' => $data['metadata'] ?? [],
        ]);
    }

    public function find(Audio $audio): Audio
    {
        return $audio;
    }

    public function update(Audio $audio, array $data): Audio
    {
        $this->validateAudioData($data, false);
        $audio->update($data);
        return $audio;
    }

    public function delete(Audio $audio): void
    {
        $audio->delete();
    }

    public function speechToText($file, int $userId): array
    {
        $path = $file->store('audio', 'public');
        $transcript = 'Transcripción del audio: ' . $file->getClientOriginalName();

        $audio = Audio::create([
            'user_id' => $userId,
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'type' => 'speech',
            'transcript' => $transcript,
        ]);

        return [
            'audio' => $audio,
            'transcript' => $transcript,
        ];
    }

    public function textToSpeech(array $data, int $userId): array
    {
        $this->validateTextToSpeechData($data);

        $audioPath = 'audio/speech/' . time() . '.mp3';

        $audio = Audio::create([
            'user_id' => $userId,
            'file_path' => $audioPath,
            'original_name' => 'tts_' . time() . '.mp3',
            'mime_type' => 'audio/mpeg',
            'size' => 0,
            'type' => 'speech',
            'metadata' => [
                'text' => $data['text'],
                'voice' => $data['voice'] ?? null,
                'language' => $data['language'] ?? 'es',
            ],
        ]);

        return [
            'audio' => $audio,
            'message' => 'Audio generado exitosamente',
        ];
    }

    private function validateAudioData(array $data, bool $required = true): void
    {
        $rules = [
            'type' => ($required ? 'required|' : '') . 'string|in:speech,voice_note,emergency',
            'transcript' => 'nullable|string',
            'duration' => 'nullable|integer',
            'metadata' => 'nullable|array',
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    private function validateTextToSpeechData(array $data): void
    {
        $validator = Validator::make($data, [
            'text' => 'required|string|max:500',
            'voice' => 'nullable|string|in:male,female,child',
            'language' => 'nullable|string|in:es,en,fr',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
