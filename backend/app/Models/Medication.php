<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Medication extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'medical_record_id',
        'patient_id',
        'doctor_id',
        'name',
        'brand_name',
        'generic_name',
        'dosage',
        'frequency',
        'route', // oral, intravenous, topical, inhalation, etc.
        'strength',
        'unit', // mg, ml, mcg, etc.
        'quantity',
        'refills',
        'start_date',
        'end_date',
        'time_of_day', // morning, afternoon, evening, night, specific times
        'with_food', // before, after, with, without
        'instructions',
        'side_effects',
        'contraindications',
        'interactions',
        'status', // active, completed, paused, discontinued
        'is_prn', // as needed
        'is_controlled',
        'is_emergency_medication',
        'pharmacy',
        'prescription_number',
        'metadata',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'time_of_day' => 'json',
        'side_effects' => 'json',
        'contraindications' => 'json',
        'interactions' => 'json',
        'is_prn' => 'boolean',
        'is_controlled' => 'boolean',
        'is_emergency_medication' => 'boolean',
        'metadata' => 'json',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function medicalRecord(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForPatient($query, int $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    public function scopeForDoctor($query, int $doctorId)
    {
        return $query->where('doctor_id', $doctorId);
    }

    public function scopeControlled($query)
    {
        return $query->where('is_controlled', true);
    }

    public function scopeEmergency($query)
    {
        return $query->where('is_emergency_medication', true);
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeExpiringSoon($query)
    {
        return $query->where('status', 'active')
                     ->whereNotNull('end_date')
                     ->whereBetween('end_date', [now(), now()->addDays(30)]);
    }

    public function scopePrn($query)
    {
        return $query->where('is_prn', true);
    }

    public function isAccessibleByUser(User $user): bool
    {
        return $this->user_id === $user->id ||
               $this->patient_id === $user->id ||
               $this->doctor_id === $user->id;
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && 
               (!$this->end_date || $this->end_date->isFuture());
    }

    public function needsRefill(): bool
    {
        return $this->refills > 0 && $this->quantity <= ($this->refills * 0.2);
    }

    public function addReminder(array $reminderData): array
    {
        return $reminderData;
    }

    public function recordAdministration(array $administrationData): array
    {
        return $administrationData;
    }

    public function pause(): void
    {
        $this->update(['status' => 'paused']);
    }

    public function resume(): void
    {
        $this->update(['status' => 'active']);
    }

    public function discontinue(): void
    {
        $this->update(['status' => 'discontinued']);
    }

    public function getFormattedDosage(): string
    {
        return "{$this->strength} {$this->unit} {$this->dosage}";
    }

    public function getFrequencyText(): string
    {
        return match($this->frequency) {
            'once_daily' => 'Una vez al día',
            'twice_daily' => 'Dos veces al día',
            'three_times_daily' => 'Tres veces al día',
            'four_times_daily' => 'Cuatro veces al día',
            'weekly' => 'Semanal',
            'monthly' => 'Mensual',
            'as_needed' => 'Según sea necesario',
            default => $this->frequency,
        };
    }
}
