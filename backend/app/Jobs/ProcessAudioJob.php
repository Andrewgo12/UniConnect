<?php

namespace App\Jobs;

use App\Models\Audio;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessAudioJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 300;

    public function __construct(public Audio $audio, public array $options = []) {}

    public function handle(): void
    {
        try {
            $transcript = match ($this->audio->type) {
                'speech'        => 'Transcripción del audio: ' . $this->audio->title,
                'voice_note'    => 'Nota de voz procesada: ' . $this->audio->title,
                'emergency'     => 'Alerta de emergencia detectada en audio',
                default         => 'Audio procesado: ' . $this->audio->title,
            };

            $this->audio->update(['transcript' => $transcript, 'is_processed' => true]);

            Log::info('Audio processing completed', ['audio_id' => $this->audio->id]);
        } catch (\Exception $e) {
            Log::error('Audio processing failed', ['audio_id' => $this->audio->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('ProcessAudioJob failed', ['audio_id' => $this->audio->id, 'error' => $exception->getMessage()]);
    }
}
