<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'type', // profile, sign_language, emergency, medical, general
        'priority',
        'file_path',
        'original_name',
        'mime_type',
        'size',
        'width',
        'height',
        'alt_text',
        'tags',
        'is_public',
        'is_approved',
        'usage_count',
        'metadata',
        'created_by',
    ];

    protected $casts = [
        'tags' => 'array',
        'metadata' => 'json',
        'is_public' => 'boolean',
        'is_approved' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByTag($query, string $tag)
    {
        return $query->whereJsonContains('tags', $tag);
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    public function isFavoritedByUser(User $user): bool
    {
        return $this->favorites()->where('user_id', $user->id)->exists();
    }

    public function addToFavorites(User $user): void
    {
        $this->favorites()->firstOrCreate([
            'user_id' => $user->id,
        ]);
    }

    public function removeFromFavorites(User $user): void
    {
        $this->favorites()->where('user_id', $user->id)->delete();
    }

    public function recordView(User $user): void
    {
        $this->views()->firstOrCreate([
            'user_id' => $user->id,
        ]);
    }

    public function getAspectRatio(): ?float
    {
        if (!$this->width || !$this->height) {
            return null;
        }
        return $this->width / $this->height;
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

    public function isAccessibleForUser(User $user): bool
    {
        return $this->user_id === $user->id || $this->is_public;
    }
}
