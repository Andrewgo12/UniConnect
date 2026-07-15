<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'type', // individual, group, support, emergency
        'status', // active, archived, closed
        'priority', // low, medium, high, urgent
        'category', // general, medical, emergency, accessibility, technical
        'settings',
        'metadata',
        'created_by',
        'closed_by',
        'closed_at',
        'last_message_id',
        'is_public',
        'is_pinned',
        'is_muted',
    ];

    protected $casts = [
        'settings' => 'json',
        'metadata' => 'json',
        'closed_at' => 'datetime',
        'is_public' => 'boolean',
        'is_pinned' => 'boolean',
        'is_muted' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function closer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'conversation_participants')
                    ->withPivot('joined_at', 'left_at', 'role', 'permissions')
                    ->withTimestamps();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at');
    }

    public function lastMessage(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'last_message_id');
    }

    public function unreadMessages(User $user): HasMany
    {
        return $this->hasMany(Message::class)
                    ->whereDoesntHave('reads', function ($query) use ($user) {
                        $query->where('user_id', $user->id);
                    });
    }

    public function getSettingsAttribute($value)
    {
        return $value ? json_decode($value, true) : [
            'allow_file_sharing' => true,
            'allow_voice_messages' => true,
            'allow_video_calls' => false,
            'auto_translate' => true,
            'accessibility_mode' => 'standard',
            'max_participants' => 10,
        ];
    }

    public function setSettingsAttribute($value)
    {
        $this->attributes['settings'] = is_array($value) ? json_encode($value) : $value;
    }

    public function getMetadataAttribute($value)
    {
        return $value ? json_decode($value, true) : [
            'location' => null,
            'emergency_type' => null,
            'medical_priority' => null,
            'accessibility_requirements' => [],
        ];
    }

    public function setMetadataAttribute($value)
    {
        $this->attributes['metadata'] = is_array($value) ? json_encode($value) : $value;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeUrgent($query)
    {
        return $query->whereIn('priority', ['high', 'urgent']);
    }

    public function scopeEmergency($query)
    {
        return $query->where('category', 'emergency');
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('created_by', $userId);
    }

    public function isParticipant(User $user): bool
    {
        return $this->participants()->where('user_id', $user->id)->exists();
    }

    public function getUnreadCount(User $user): int
    {
        return $this->unreadMessages($user)->count();
    }

    public function addParticipant(User $user, string $role = 'member', array $permissions = []): void
    {
        $this->participants()->attach($user->id, [
            'role' => $role,
            'permissions' => json_encode($permissions),
            'joined_at' => now(),
        ]);
    }

    public function removeParticipant(User $user): void
    {
        $this->participants()->updateExistingPivot($user->id, [
            'left_at' => now(),
        ]);
    }
}
