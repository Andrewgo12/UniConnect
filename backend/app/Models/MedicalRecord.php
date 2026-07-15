<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'patient_id',
        'doctor_id',
        'title',
        'description',
        'type', // diagnosis, treatment, prescription, lab_result, imaging, vaccination, allergy
        'category', // general, emergency, chronic, acute, preventive
        'severity', // mild, moderate, severe, critical
        'status', // active, resolved, chronic, monitoring
        'diagnosis_code',
        'diagnosis',
        'treatment_plan',
        'treatment',
        'created_by',
        'medications',
        'symptoms',
        'notes',
        'follow_up_date',
        'is_confidential',
        'is_emergency',
        'metadata',
        'accessibility_data',
    ];

    protected $casts = [
        'medications' => 'array',
        'symptoms' => 'array',
        'follow_up_date' => 'datetime',
        'is_confidential' => 'boolean',
        'is_emergency' => 'boolean',
        'metadata' => 'json',
        'accessibility_data' => 'json',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function prescribedMedications(): HasMany
    {
        return $this->hasMany(Medication::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeEmergency($query)
    {
        return $query->where('is_emergency', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeBySeverity($query, string $severity)
    {
        return $query->where('severity', $severity);
    }

    public function scopeForPatient($query, int $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    public function scopeForDoctor($query, int $doctorId)
    {
        return $query->where('doctor_id', $doctorId);
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent($query)
    {
        return $query->where('created_at', '>=', now()->subDays(30));
    }

    public function isAccessibleByUser(User $user): bool
    {
        return $this->user_id === $user->id ||
               $this->patient_id === $user->id ||
               $this->doctor_id === $user->id;
    }

    public function addMedication(array $medicationData): Medication
    {
        return $this->prescribedMedications()->create($medicationData);
    }

    public function scheduleAppointment(array $appointmentData): Appointment
    {
        return $this->appointments()->create($appointmentData);
    }

    public function markAsResolved(): void
    {
        $this->update(['status' => 'resolved']);
    }

    public function markAsEmergency(): void
    {
        $this->update(['is_emergency' => true, 'severity' => 'critical']);
    }
}
