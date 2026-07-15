<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class Emergency extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'description',
        'location',
        'status',
        'resolved_at',
        'acknowledged_at',
        'severity',
        'latitude',
        'longitude',
        'contact_name',
        'contact_phone',
        'contact_relationship',
        'medical_conditions',
        'accessibility_needs',
        'metadata',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'acknowledged_at' => 'datetime',
        'medical_conditions' => 'array',
        'accessibility_needs' => 'array',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeCritical($query)
    {
        return $query->where('severity', 'critical');
    }

    public function scopeMedical($query)
    {
        return $query->where('type', 'medical');
    }

    public function scopeSecurity($query)
    {
        return $query->where('type', 'security');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeBySeverity($query, string $severity)
    {
        return $query->where('severity', $severity);
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function getResponseTimeAttribute(): ?int
    {
        if (! $this->acknowledged_at || ! $this->created_at) {
            return null;
        }

        return (int) round($this->created_at->diffInMinutes($this->acknowledged_at));
    }

    public function getMediaAttribute(): Collection
    {
        $batches = data_get($this->metadata ?? [], 'media', []);

        $files = [];
        foreach ($batches as $paths) {
            foreach ((array) $paths as $path) {
                $files[] = $path;
            }
        }

        return collect($files);
    }

    /**
     * @param  list<string>|array<int, string>  $files
     */
    public function addMediaFromRequest(string $key, array $files): void
    {
        $meta = $this->metadata ?? [];
        $media = $meta['media'] ?? [];
        $media[$key] = array_values(array_unique(array_merge($media[$key] ?? [], $files)));
        $meta['media'] = $media;
        $this->metadata = $meta;
        $this->save();
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isCritical(): bool
    {
        return $this->severity === 'critical';
    }

    public function resolve(User $resolver, string $notes = ''): void
    {
        $this->update([
            'status' => 'resolved',
            'resolved_at' => now(),
        ]);
    }

    public function cancel(User $user, string $reason = ''): void
    {
        $this->update([
            'status' => 'cancelled',
        ]);
    }

    public function addParticipant(User $user, string $role = 'responder'): void
    {
        // participants relation removed - EmergencyParticipant model not implemented
    }

    public function getResponseDuration(): ?int
    {
        return $this->resolved_at && $this->created_at
            ? $this->resolved_at->diffInSeconds($this->created_at)
            : null;
    }
}
