<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'avatar',
        'blind',
        'deaf',
        'mute',
        'preferences',
    ];

    protected $casts = [
        'blind' => 'boolean',
        'deaf' => 'boolean',
        'mute' => 'boolean',
        'preferences' => 'json',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function medicalRecords(): HasMany
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function getAccessibilityNeedsAttribute($value)
    {
        return $value ? json_decode($value, true) : [
            'screen_reader' => false,
            'high_contrast' => false,
            'vibration' => false,
            'sign_language' => false,
            'keyboard_navigation' => false,
            'voice_commands' => false,
            'large_text' => false,
        ];
    }

    public function setAccessibilityNeedsAttribute($value)
    {
        $this->attributes['accessibility_needs'] = is_array($value) ? json_encode($value) : $value;
    }

    public function getEmergencyContactsAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    public function setEmergencyContactsAttribute($value)
    {
        $this->attributes['emergency_contacts'] = is_array($value) ? json_encode($value) : $value;
    }

    public function getMedicalInformationAttribute($value)
    {
        return $value ? json_decode($value, true) : [
            'blood_type' => null,
            'allergies' => [],
            'medications' => [],
            'conditions' => [],
            'emergency_notes' => '',
        ];
    }

    public function setMedicalInformationAttribute($value)
    {
        $this->attributes['medical_information'] = is_array($value) ? json_encode($value) : $value;
    }

    public function getPreferencesAttribute($value)
    {
        return $value ? json_decode($value, true) : [
            'theme' => 'light',
            'language' => 'es-CO',
            'notifications' => true,
            'auto_translate' => true,
            'show_disability_badge' => false,
        ];
    }

    public function setPreferencesAttribute($value)
    {
        $this->attributes['preferences'] = is_array($value) ? json_encode($value) : $value;
    }

    public function getSocialLinksAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    public function setSocialLinksAttribute($value)
    {
        $this->attributes['social_links'] = is_array($value) ? json_encode($value) : $value;
    }
}
