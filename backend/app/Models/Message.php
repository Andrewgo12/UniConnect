<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'user_id',
        'content',
        'type', // text, voice, video, image, file, sign_language, emergency
        'status', // sent, delivered, read, failed
        'priority', // low, medium, high, urgent
        'metadata',
        'parent_id', // for replies
        'edited_at',
        'deleted_at',
        'is_edited',
        'is_deleted',
        'is_pinned',
        'accessibility_data',
        'voice_duration',
        'language',
    ];

    protected $casts = [
        'metadata' => 'json',
        'accessibility_data' => 'json',
        'edited_at' => 'datetime',
        'deleted_at' => 'datetime',
        'is_edited' => 'boolean',
        'is_deleted' => 'boolean',
        'is_pinned' => 'boolean',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Message::class, 'parent_id');
    }

    public function getMetadataAttribute($value)
    {
        return $value ? json_decode($value, true) : [
            'location' => null,
            'device_info' => null,
            'accessibility_mode' => 'standard',
            'language' => 'es-CO',
            'duration' => null,
            'file_size' => null,
        ];
    }

    public function setMetadataAttribute($value)
    {
        $this->attributes['metadata'] = is_array($value) ? json_encode($value) : $value;
    }

    public function getAccessibilityDataAttribute($value)
    {
        return $value ? json_decode($value, true) : [
            'screen_reader_text' => null,
            'vibration_pattern' => [],
            'sign_language_url' => null,
            'high_contrast_version' => null,
            'large_text_version' => null,
        ];
    }

    public function setAccessibilityDataAttribute($value)
    {
        $this->attributes['accessibility_data'] = is_array($value) ? json_encode($value) : $value;
    }

    public function scopeActive($query)
    {
        return $query->where('is_deleted', false);
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeRead($query)
    {
        return $query->where('status', 'read');
    }

    public function scopeUnread($query)
    {
        return $query->where('status', '!=', 'read');
    }

    public function scopeUrgent($query)
    {
        return $query->whereIn('priority', ['high', 'urgent']);
    }

    public function scopeEmergency($query)
    {
        return $query->where('type', 'emergency');
    }

}
