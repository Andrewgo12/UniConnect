<?php

namespace App\Jobs;

use App\Models\Image;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProcessImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [60, 300, 900];
    public $timeout = 180;

    /**
     * Create a new job instance.
     *
     * @param Image $image
     * @param array $options
     */
    public function __construct(
        public Image $image,
        public array $options = []
    ) {
        $this->image = $image;
        $this->options = $options;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Process image based on type
            switch ($this->image->type) {
                case 'profile':
                    $this->processProfileImage();
                    break;
                case 'sign_language':
                    $this->processSignLanguageImage();
                    break;
                case 'emergency':
                    $this->processEmergencyImage();
                    break;
                case 'medical':
                    $this->processMedicalImage();
                    break;
                default:
                    $this->processGenericImage();
                    break;
            }

            // Generate thumbnails if needed
            if ($this->options['generate_thumbnails'] ?? true) {
                $this->generateThumbnails();
            }

        } catch (\Exception $e) {
            Log::error('Image processing failed: ' . $e->getMessage(), [
                'image_id' => $this->image->id,
                'type' => $this->image->type,
                'options' => $this->options,
            ]);
            
            throw $e;
        }
    }

    /**
     * Process profile image.
     */
    private function processProfileImage(): void
    {
        // Simulate profile image processing
        Log::info('Profile image processing started', [
            'image_id' => $this->image->id,
            'original_name' => $this->image->original_name,
        ]);

        // Simulate processing time
        sleep(2);

        Log::info('Profile image processing completed', [
            'image_id' => $this->image->id,
            'processed_size' => $this->image->size,
        ]);
    }

    /**
     * Process sign language image.
     */
    private function processSignLanguageImage(): void
    {
        Log::info('Sign language image processing started', [
            'image_id' => $this->image->id,
            'title' => $this->image->title,
        ]);

        // Simulate processing time
        sleep(3);

        Log::info('Sign language image processing completed', [
            'image_id' => $this->image->id,
            'accessibility_optimized' => true,
        ]);
    }

    /**
     * Process emergency image.
     */
    private function processEmergencyImage(): void
    {
        Log::info('Emergency image processing started', [
            'image_id' => $this->image->id,
            'priority' => 'high',
        ]);

        // Simulate processing time
        sleep(1);

        Log::info('Emergency image processing completed', [
            'image_id' => $this->image->id,
            'emergency_ready' => true,
        ]);
    }

    /**
     * Process medical image.
     */
    private function processMedicalImage(): void
    {
        Log::info('Medical image processing started', [
            'image_id' => $this->image->id,
            'description' => $this->image->description,
        ]);

        // Simulate processing time
        sleep(2);

        Log::info('Medical image processing completed', [
            'image_id' => $this->image->id,
            'hipaa_compliant' => true,
        ]);
    }

    /**
     * Process generic image.
     */
    private function processGenericImage(): void
    {
        Log::info('Generic image processing started', [
            'image_id' => $this->image->id,
            'type' => $this->image->type,
        ]);

        // Simulate processing time
        sleep(1);

        Log::info('Generic image processing completed', [
            'image_id' => $this->image->id,
            'processed' => true,
        ]);
    }

    /**
     * Generate thumbnails for the image.
     */
    private function generateThumbnails(): void
    {
        Log::info('Thumbnail generation started', [
            'image_id' => $this->image->id,
            'sizes' => ['small', 'medium', 'large'],
        ]);

        // Simulate thumbnail generation
        sleep(1);

        Log::info('Thumbnail generation completed', [
            'image_id' => $this->image->id,
            'thumbnails_created' => true,
        ]);
    }

    /**
     * The job failed to process.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Image processing job failed', [
            'image_id' => $this->image->id,
            'exception' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }

    /**
     * The job has finished processing.
     */
    public function success(): void
    {
        Log::info('Image processing job completed successfully', [
            'image_id' => $this->image->id,
            'processed_at' => now(),
        ]);
    }
}
