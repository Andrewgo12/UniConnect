<?php

namespace App\Models;

use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable implements MustVerifyEmail
{
    use CanResetPassword, HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'date_of_birth',
        'gender',
        'profile_type',
        'accessibility_needs',
        'accessibility_settings',
        'preferences',
        'is_active',
        'email_verified_at',
        'phone_verified_at',
        'last_login_at',
        'last_login_ip',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'two_factor_confirmed_at' => 'datetime',
        'accessibility_needs' => 'json',
        'accessibility_settings' => 'json',
        'preferences' => 'json',
        'is_active' => 'boolean',
    ];

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function conversations(): BelongsToMany
    {
        return $this->belongsToMany(Conversation::class, 'conversation_participants')
                    ->withPivot('joined_at', 'left_at', 'role')
                    ->withTimestamps();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function emergencies(): HasMany
    {
        return $this->hasMany(Emergency::class);
    }

    public function medicalRecords(): HasMany
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function accessibilityLogs(): HasMany
    {
        return $this->hasMany(AccessibilityLog::class);
    }

    public function securityLogs(): HasMany
    {
        return $this->hasMany(SecurityLog::class);
    }

    public function getAccessibilitySettingsAttribute($value)
    {
        return $value ? json_decode($value, true) : [
            'screen_reader' => false,
            'high_contrast' => false,
            'vibration' => true,
            'sign_language' => false,
            'keyboard_navigation' => false,
        ];
    }

    public function setAccessibilitySettingsAttribute($value)
    {
        $this->attributes['accessibility_settings'] = is_array($value) ? json_encode($value) : $value;
    }

    public function getAccessibilityNeedsAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    public function setAccessibilityNeedsAttribute($value)
    {
        $this->attributes['accessibility_needs'] = is_array($value) ? json_encode($value) : $value;
    }

    public function getPreferencesAttribute($value)
    {
        return $value ? json_decode($value, true) : [
            'language' => 'es-CO',
            'theme' => 'light',
            'notifications' => true,
            'auto_translate' => true,
        ];
    }

    public function setPreferencesAttribute($value)
    {
        $this->attributes['preferences'] = is_array($value) ? json_encode($value) : $value;
    }
}
