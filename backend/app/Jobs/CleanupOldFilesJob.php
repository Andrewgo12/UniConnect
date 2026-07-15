<?php

namespace App\Jobs;

use App\Models\Audio;
use App\Models\Image;
use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CleanupOldFilesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $timeout = 3600; // 1 hour

    /**
     * Create a new job instance.
     *
     * @param int $daysOld
     * @param array $options
     */
    public function __construct(
        public int $daysOld = 30,
        public array $options = []
    ) {
        $this->daysOld = $daysOld;
        $this->options = $options;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $cutoffDate = now()->subDays($this->daysOld);
            $deletedFiles = [];

            // Cleanup old audio files
            $oldAudio = Audio::where('created_at', '<', $cutoffDate)
                ->where('is_processed', true)
                ->get();

            foreach ($oldAudio as $audio) {
                if ($audio->file_path && Storage::exists($audio->file_path)) {
                    Storage::delete($audio->file_path);
                    $deletedFiles[] = $audio->file_path;
                    $audio->delete();
                }
            }

            // Cleanup old image files
            $oldImages = Image::where('created_at', '<', $cutoffDate)
                ->where('is_approved', true)
                ->get();

            foreach ($oldImages as $image) {
                if ($image->file_path && Storage::exists($image->file_path)) {
                    Storage::delete($image->file_path);
                    $deletedFiles[] = $image->file_path;
                    $image->delete();
                }
            }

            // Cleanup old message attachments
            $oldMessages = Message::where('created_at', '<', $cutoffDate)
                ->whereNotNull('metadata')
                ->get();

            foreach ($oldMessages as $message) {
                $metadata = $message->metadata ?? [];
                if (isset($metadata['file_path']) && Storage::exists($metadata['file_path'])) {
                    Storage::delete($metadata['file_path']);
                    $deletedFiles[] = $metadata['file_path'];
                }
            }

            Log::info('Cleanup job completed', [
                'days_old' => $this->daysOld,
                'cutoff_date' => $cutoffDate,
                'deleted_audio_count' => $oldAudio->count(),
                'deleted_images_count' => $oldImages->count(),
                'deleted_messages_count' => $oldMessages->count(),
                'total_files_deleted' => count($deletedFiles),
                'deleted_files' => $deletedFiles,
            ]);

        } catch (\Exception $e) {
            Log::error('Cleanup job failed: ' . $e->getMessage(), [
                'days_old' => $this->daysOld,
                'exception' => $e->getTraceAsString(),
            ]);
            
            throw $e;
        }
    }

    /**
     * The job failed to process.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Cleanup job failed', [
            'days_old' => $this->daysOld,
            'exception' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }

    /**
     * The job has finished processing.
     */
    public function success(): void
    {
        Log::info('Cleanup job completed successfully', [
            'days_old' => $this->daysOld,
            'completed_at' => now(),
        ]);
    }
}
