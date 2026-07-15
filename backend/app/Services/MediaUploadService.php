<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\UploadedFile;
use App\Models\SignLanguage;
use App\Models\Audio;
use App\Models\Image;

class MediaUploadService
{
    /**
     * Upload sign language video.
     */
    public static function uploadSignLanguage(UploadedFile $file, array $data): array
    {
        try {
            $validation = Validator::make([
                'file' => $file,
                'title' => $data['title'] ?? '',
                'description' => $data['description'] ?? '',
                'category' => $data['category'] ?? 'basic',
                'difficulty_level' => $data['difficulty_level'] ?? 'beginner',
                'region' => $data['region'] ?? 'colombian',
                'tags' => $data['tags'] ?? [],
                'is_public' => $data['is_public'] ?? false,
                'language' => $data['language'] ?? 'es-CO',
                'transcript' => $data['transcript'] ?? '',
            ], [
                'file' => 'required|mimes:mp4,avi,mov,wmv|max:102400', // 100MB max
                'title' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'category' => 'required|in:basic,medical,emergency,education,custom',
                'difficulty_level' => 'required|in:beginner,intermediate,advanced',
                'region' => 'required|in:colombian,international,local',
                'tags' => 'nullable|array',
                'is_public' => 'boolean',
                'language' => 'required|string|max:10',
                'transcript' => 'nullable|string|max:5000',
            ], [
                'file.required' => 'El archivo de video es requerido',
                'file.mimes' => 'El archivo debe ser un video (mp4, avi, mov, wmv)',
                'file.max' => 'El archivo no debe ser mayor a 100MB',
                'title.required' => 'El título es requerido',
                'title.max' => 'El título no debe exceder 255 caracteres',
                'category.required' => 'La categoría es requerida',
                'category.in' => 'Categoría inválida',
                'difficulty_level.required' => 'El nivel de dificultad es requerido',
                'difficulty_level.in' => 'Nivel de dificultad inválido',
                'region.required' => 'La región es requerida',
                'region.in' => 'Región inválida',
                'language.required' => 'El idioma es requerido',
                'language.max' => 'El idioma no debe exceder 10 caracteres',
                'transcript.max' => 'La transcripción no debe exceder 5000 caracteres',
            ]);

            if ($validation->fails()) {
                return [
                    'success' => false,
                    'errors' => $validation->errors(),
                    'message' => 'Error de validación',
                ];
            }

            // Upload file
            $path = $file->store('sign_languages', 'public');
            
            // Generate thumbnail
            $thumbnailPath = self::generateThumbnail($file, 'sign_languages/thumbnails');

            // Create sign language record
            $signLanguage = SignLanguage::create([
                'user_id' => auth()->id(),
                'title' => $data['title'],
                'description' => $data['description'],
                'category' => $data['category'],
                'difficulty_level' => $data['difficulty_level'],
                'region' => $data['region'],
                'video_url' => Storage::url($path),
                'thumbnail_url' => Storage::url($thumbnailPath),
                'duration' => self::getVideoDuration($file),
                'tags' => $data['tags'],
                'is_public' => $data['is_public'],
                'language' => $data['language'],
                'transcript' => $data['transcript'],
            ]);

            Log::info('Sign language video uploaded successfully', [
                'user_id' => auth()->id(),
                'title' => $data['title'],
                'category' => $data['category'],
                'file_path' => $path,
            ]);

            return [
                'success' => true,
                'message' => 'Video de lenguaje de señas subido exitosamente',
                'data' => $signLanguage,
            ];

        } catch (\Exception $e) {
            Log::error('Failed to upload sign language video', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return [
                'success' => false,
                'message' => 'Error al subir el video de lenguaje de señas',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Upload audio file.
     */
    public static function uploadAudio(UploadedFile $file, array $data): array
    {
        try {
            $validation = Validator::make([
                'file' => $file,
                'title' => $data['title'] ?? '',
                'description' => $data['description'] ?? '',
                'type' => $data['type'] ?? 'speech',
                'quality' => $data['quality'] ?? 'medium',
                'language' => $data['language'] ?? 'es-CO',
                'transcript' => $data['transcript'] ?? '',
                'is_public' => $data['is_public'] ?? false,
                'metadata' => $data['metadata'] ?? [],
            ], [
                'file.required' => 'El archivo de audio es requerido',
                'file.mimes' => 'El archivo debe ser un audio (mp3, wav, m4a, aac)',
                'file.max' => 'El archivo no debe ser mayor a 50MB',
                'title.required' => 'El título es requerido',
                'title.max' => 'El título no debe exceder 255 caracteres',
                'type.required' => 'El tipo es requerido',
                'type.in' => 'Tipo inválido',
                'quality.required' => 'La calidad es requerida',
                'quality.in' => 'Calidad inválida',
                'language.required' => 'El idioma es requerido',
                'language.max' => 'El idioma no debe exceder 10 caracteres',
                'transcript.max' => 'La transcripción no debe exceder 5000 caracteres',
            ]);

            if ($validation->fails()) {
                return [
                    'success' => false,
                    'errors' => $validation->errors(),
                    'message' => 'Error de validación',
                ];
            }

            // Upload file
            $path = $file->store('audios', 'public');

            // Process audio
            $processedData = self::processAudio($file, $data);

            // Create audio record
            $audio = Audio::create([
                'user_id' => auth()->id(),
                'title' => $data['title'],
                'description' => $data['description'],
                'type' => $data['type'],
                'file_path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'duration' => $processedData['duration'],
                'language' => $data['language'],
                'quality' => $data['quality'],
                'transcript' => $data['transcript'],
                'is_public' => $data['is_public'],
                'metadata' => array_merge($processedData, $data['metadata'] ?? []),
            ]);

            Log::info('Audio file uploaded successfully', [
                'user_id' => auth()->id(),
                'title' => $data['title'],
                'type' => $data['type'],
                'file_path' => $path,
            ]);

            return [
                'success' => true,
                'message' => 'Archivo de audio subido exitosamente',
                'data' => $audio,
            ];

        } catch (\Exception $e) {
            Log::error('Failed to upload audio file', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return [
                'success' => false,
                'message' => 'Error al subir el archivo de audio',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Upload image file.
     */
    public static function uploadImage(UploadedFile $file, array $data): array
    {
        try {
            $validation = Validator::make([
                'file' => $file,
                'title' => $data['title'] ?? '',
                'description' => $data['description'] ?? '',
                'type' => $data['type'] ?? 'general',
                'tags' => $data['tags'] ?? [],
                'alt_text' => $data['alt_text'] ?? '',
                'is_public' => $data['is_public'] ?? false,
                'language' => $data['language'] ?? 'es-CO',
                'metadata' => $data['metadata'] ?? [],
            ], [
                'file.required' => 'El archivo de imagen es requerido',
                'file.mimes' => 'El archivo debe ser una imagen (jpg, jpeg, png, gif, webp)',
                'file.max' => 'El archivo no debe ser mayor a 20MB',
                'title.required' => 'El título es requerido',
                'title.max' => 'El título no debe exceder 255 caracteres',
                'type.required' => 'El tipo es requerido',
                'type.in' => 'Tipo inválido',
                'alt_text.max' => 'El texto alternativo no debe exceder 500 caracteres',
                'language.required' => 'El idioma es requerido',
                'language.max' => 'El idioma no debe exceder 10 caracteres',
            ]);

            if ($validation->fails()) {
                return [
                    'success' => false,
                    'errors' => $validation->errors(),
                    'message' => 'Error de validación',
                ];
            }

            // Upload file
            $path = $file->store('images', 'public');

            // Process image
            $processedData = self::processImage($file, $data);

            // Create image record
            $image = Image::create([
                'user_id' => auth()->id(),
                'title' => $data['title'],
                'description' => $data['description'],
                'type' => $data['type'],
                'file_path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'width' => $processedData['width'],
                'height' => $processedData['height'],
                'alt_text' => $data['alt_text'],
                'tags' => $data['tags'],
                'is_public' => $data['is_public'],
                'language' => $data['language'],
                'metadata' => array_merge($processedData, $data['metadata'] ?? []),
            ]);

            Log::info('Image file uploaded successfully', [
                'user_id' => auth()->id(),
                'title' => $data['title'],
                'type' => $data['type'],
                'file_path' => $path,
            ]);

            return [
                'success' => true,
                'message' => 'Archivo de imagen subido exitosamente',
                'data' => $image,
            ];

        } catch (\Exception $e) {
            Log::error('Failed to upload image file', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return [
                'success' => false,
                'message' => 'Error al subir el archivo de imagen',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Generate thumbnail for video.
     */
    private static function generateThumbnail(UploadedFile $file, string $outputPath): string
    {
        try {
            $ffmpeg = \FFMpeg\FFMpeg::create();
            $video = $ffmpeg->open($file->getRealPath());
            
            $thumbnailPath = $outputPath . '/' . uniqid() . '.jpg';
            
            $video->frame(\FFMpeg\Coordinate\TimeCode::fromSeconds(1))
                ->save($thumbnailPath);

            Log::info('Thumbnail generated successfully', [
                'original_file' => $file->getClientOriginalName(),
                'thumbnail_path' => $thumbnailPath,
            ]);

            return $thumbnailPath;

        } catch (\Exception $e) {
            Log::error('Failed to generate thumbnail', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName(),
            ]);

            return '';
        }
    }

    /**
     * Get video duration.
     */
    private static function getVideoDuration(UploadedFile $file): float
    {
        try {
            $ffmpeg = \FFMpeg\FFMpeg::create();
            $video = $ffmpeg->open($file->getRealPath());
            $duration = $video->getFFProbe()->getDuration();

            Log::info('Video duration retrieved', [
                'file' => $file->getClientOriginalName(),
                'duration' => $duration,
            ]);

            return $duration;

        } catch (\Exception $e) {
            Log::error('Failed to get video duration', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName(),
            ]);

            return 0.0;
        }
    }

    /**
     * Process audio file.
     */
    private static function processAudio(UploadedFile $file, array $data): array
    {
        try {
            $ffmpeg = \FFMpeg\FFMpeg::create();
            $audio = $ffmpeg->open($file->getRealPath());
            
            $duration = $audio->getFFProbe()->getDuration();
            $bitrate = $audio->getFFProbe()->getAudioStreams()[0]->getBitRate() ?? 128000;
            
            // Convert to standard format if needed
            $processedPath = 'processed/audios/' . uniqid() . '.mp3';
            
            if ($file->getMimeType() !== 'audio/mpeg') {
                $audio->save(new \FFMpeg\Format\Audio\Mp3(), $processedPath);
            }

            Log::info('Audio processed successfully', [
                'original_file' => $file->getClientOriginalName(),
                'duration' => $duration,
                'bitrate' => $bitrate,
                'processed_path' => $processedPath,
            ]);

            return [
                'duration' => $duration,
                'bitrate' => $bitrate,
                'processed_path' => $processedPath,
                'quality' => $data['quality'],
            ];

        } catch (\Exception $e) {
            Log::error('Failed to process audio', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName(),
            ]);

            return [
                'duration' => 0.0,
                'bitrate' => 0,
                'processed_path' => '',
                'quality' => $data['quality'],
            ];
        }
    }

    /**
     * Process image file.
     */
    private static function processImage(UploadedFile $file, array $data): array
    {
        try {
            $imageInfo = getimagesize($file->getRealPath());
            
            if ($imageInfo === false) {
                throw new \Exception('Invalid image file');
            }

            $width = $imageInfo[0];
            $height = $imageInfo[1];

            // Generate different sizes for accessibility
            $sizes = [
                'thumbnail' => [150, 150],
                'medium' => [500, 500],
                'large' => [1200, 1200],
            ];

            $processedPaths = [];
            
            foreach ($sizes as $sizeName => [$width, $height]) {
                $resizedPath = 'processed/images/' . $sizeName . '_' . uniqid() . '.jpg';
                
                $image = \Intervention\Image\Facades\Image::make($file->getRealPath())
                    ->resize($width, $height, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })
                    ->save($resizedPath);

                $processedPaths[$sizeName] = $resizedPath;
            }

            Log::info('Image processed successfully', [
                'original_file' => $file->getClientOriginalName(),
                'width' => $width,
                'height' => $height,
                'processed_paths' => $processedPaths,
            ]);

            return [
                'width' => $width,
                'height' => $height,
                'processed_paths' => $processedPaths,
                'aspect_ratio' => $width / $height,
            ];

        } catch (\Exception $e) {
            Log::error('Failed to process image', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName(),
            ]);

            return [
                'width' => 0,
                'height' => 0,
                'processed_paths' => [],
                'aspect_ratio' => 0,
            ];
        }
    }

    /**
     * Delete media file.
     */
    public static function deleteMedia(string $filePath, string $type): bool
    {
        try {
            if (Storage::exists($filePath)) {
                Storage::delete($filePath);
                
                // Delete processed files
                $processedDir = dirname($filePath) . '/processed';
                if (Storage::exists($processedDir)) {
                    $files = Storage::allFiles($processedDir);
                    foreach ($files as $file) {
                        Storage::delete($processedDir . '/' . $file);
                    }
                }

                Log::info('Media file deleted successfully', [
                    'file_path' => $filePath,
                    'type' => $type,
                ]);

                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error('Failed to delete media file', [
                'error' => $e->getMessage(),
                'file_path' => $filePath,
            ]);

            return false;
        }
    }

    /**
     * Get media statistics.
     */
    public static function getMediaStatistics(): array
    {
        try {
            $stats = [
                'sign_languages' => SignLanguage::count(),
                'audios' => Audio::count(),
                'images' => Image::count(),
                'total_size' => self::calculateTotalSize(),
                'by_type' => [
                    'sign_languages' => SignLanguage::groupBy('category')->selectRaw('category, count(*) as count')->get(),
                    'audios' => Audio::groupBy('type')->selectRaw('type, count(*) as count')->get(),
                    'images' => Image::groupBy('type')->selectRaw('type, count(*) as count')->get(),
                ],
                'recent_uploads' => [
                    'sign_languages' => SignLanguage::latest()->take(10)->get(),
                    'audios' => Audio::latest()->take(10)->get(),
                    'images' => Image::latest()->take(10)->get(),
                ],
            ];

            Log::info('Media statistics retrieved', $stats);

            return $stats;

        } catch (\Exception $e) {
            Log::error('Failed to get media statistics', [
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Calculate total storage size.
     */
    private static function calculateTotalSize(): array
    {
        $signLanguageSize = SignLanguage::sum('size') ?? 0;
        $audioSize = Audio::sum('size') ?? 0;
        $imageSize = Image::sum('size') ?? 0;
        
        $totalSize = $signLanguageSize + $audioSize + $imageSize;
        
        return [
            'total_bytes' => $totalSize,
            'total_mb' => round($totalSize / 1048576, 2),
            'total_gb' => round($totalSize / 1073741824, 2),
            'breakdown' => [
                'sign_languages' => $signLanguageSize,
                'audios' => $audioSize,
                'images' => $imageSize,
            ],
        ];
    }
}
