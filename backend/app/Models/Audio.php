<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Audio extends Model
{
    use HasFactory;

    protected $table = 'audio';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'type', // speech, voice_note, emergency, sign_language
        'file_path',
        'original_name',
        'mime_type',
        'size',
        'duration',
        'transcript',
        'language',
        'quality', // low, medium, high
        'is_public',
        'is_processed',
        'metadata',
        'created_by',
        'priority',
    ];

    protected $casts = [
        'metadata' => 'json',
        'is_public' => 'boolean',
        'is_processed' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeProcessed($query)
    {
        return $query->where('is_processed', true);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByLanguage($query, string $language)
    {
        return $query->where('language', $language);
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function markAsProcessed(): void
    {
        $this->update(['is_processed' => true]);
    }

    public function getDurationInMinutes(): float
    {
        return $this->duration ? round($this->duration / 60, 2) : 0;
    }

    public function getFormattedSize(): string
    {
        $bytes = $this->size ?? 0;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    public function addTranscript(string $locale, string $text): void
    {
        $meta = $this->metadata ?? [];
        $transcripts = $meta['transcripts'] ?? [];
        $transcripts[$locale] = $text;
        $meta['transcripts'] = $transcripts;
        $this->metadata = $meta;
        $this->save();
    }

    public function getTranscriptsAttribute(): array
    {
        return data_get($this->metadata ?? [], 'transcripts', []);
    }

    public function isAccessibleForUser(User $user): bool
    {
        return $this->user_id === $user->id || $this->is_public;
    }
}
