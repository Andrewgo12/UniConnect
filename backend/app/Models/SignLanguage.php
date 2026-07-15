<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SignLanguage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category',
        'difficulty_level', // beginner, intermediate, advanced
        'region', // colombian, international, local
        'video_url',
        'image_url',
        'thumbnail_url',
        'duration',
        'tags',
        'is_public',
        'is_approved',
        'usage_count',
        'metadata',
        'created_by',
        'transcript',
        'language',
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

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByDifficulty($query, string $level)
    {
        return $query->where('difficulty_level', $level);
    }

    public function scopeByRegion($query, string $region)
    {
        return $query->where('region', $region);
    }

    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }
}
