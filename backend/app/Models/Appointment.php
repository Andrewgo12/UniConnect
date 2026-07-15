<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'patient_id',
        'doctor_id',
        'medical_record_id',
        'title',
        'description',
        'type', // consultation, follow_up, emergency, surgery, therapy, lab_test, imaging
        'category', // general, specialized, urgent, routine
        'status', // scheduled, confirmed, in_progress, completed, cancelled, no_show, rescheduled
        'priority', // low, medium, high, urgent
        'scheduled_date',
        'scheduled_time',
        'duration', // in minutes
        'location',
        'location_type', // physical, virtual, home_visit
        'virtual_meeting_url',
        'virtual_meeting_id',
        'notes',
        'preparation_instructions',
        'cancellation_reason',
        'rescheduling_notes',
        'reminder_sent',
        'reminder_sent_at',
        'is_confidential',
        'is_emergency',
        'metadata',
        'is_virtual',
        'meeting_link',
        'reminders',
        'completed_at',
        'cancelled_at',
        'created_by',
        'appointment_date',
    ];

    protected $casts = [
        'scheduled_date' => 'datetime',
        'scheduled_time' => 'datetime',
        'reminder_sent_at' => 'datetime',
        'is_confidential' => 'boolean',
        'is_emergency' => 'boolean',
        'metadata' => 'json',
        'is_virtual' => 'boolean',
        'reminders' => 'json',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'appointment_date' => 'datetime',
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

    public function medicalRecord(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_date', '>=', now())
                    ->whereIn('status', ['scheduled', 'confirmed']);
    }

    public function scopePast($query)
    {
        return $query->where('scheduled_date', '<', now());
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

    public function scopeVirtual($query)
    {
        return $query->where('is_virtual', true);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeEmergency($query)
    {
        return $query->where('is_emergency', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function isAccessibleByUser(User $user): bool
    {
        return $this->user_id === $user->id ||
               $this->patient_id === $user->id ||
               $this->doctor_id === $user->id;
    }

    public function isUpcoming(): bool
    {
        return $this->scheduled_date->isFuture() && 
               in_array($this->status, ['scheduled', 'confirmed']);
    }

    public function isPast(): bool
    {
        return $this->scheduled_date->isPast();
    }

    public function isVirtual(): bool
    {
        return $this->location_type === 'virtual';
    }

    public function confirm(): void
    {
        $this->update(['status' => 'confirmed']);
    }

    public function start(): void
    {
        $this->update(['status' => 'in_progress']);
    }

    public function complete(): void
    {
        $this->update(['status' => 'completed']);
    }

    public function cancel(string $reason = null): void
    {
        $this->update([
            'status' => 'cancelled',
            'cancellation_reason' => $reason
        ]);
    }

    public function markAsNoShow(): void
    {
        $this->update(['status' => 'no_show']);
    }

    public function reschedule(\DateTime $newDate, \DateTime $newTime, string $notes = null): void
    {
        $this->update([
            'scheduled_date' => $newDate,
            'scheduled_time' => $newTime,
            'status' => 'rescheduled',
            'rescheduling_notes' => $notes
        ]);
    }

    public function addReminder(array $reminderData): array
    {
        return $reminderData;
    }

    public function markReminderSent(): void
    {
        $this->update([
            'reminder_sent' => true,
            'reminder_sent_at' => now()
        ]);
    }

    public function getFormattedDateTime(): string
    {
        return $this->scheduled_date->format('d/m/Y') . ' ' . 
               $this->scheduled_time->format('H:i');
    }

    public function getDurationInHours(): float
    {
        return $this->duration / 60;
    }

    public function getEndTime(): \DateTime
    {
        $endTime = clone $this->scheduled_time;
        $endTime->add(new \DateInterval("PT{$this->duration}M"));
        return $endTime;
    }

    public function getTypeText(): string
    {
        return match($this->type) {
            'consultation' => 'Consulta',
            'follow_up' => 'Seguimiento',
            'emergency' => 'Emergencia',
            'surgery' => 'Cirugía',
            'therapy' => 'Terapia',
            'lab_test' => 'Examen de Laboratorio',
            'imaging' => 'Imagenología',
            default => $this->type,
        };
    }

    public function getStatusText(): string
    {
        return match($this->status) {
            'scheduled' => 'Programada',
            'confirmed' => 'Confirmada',
            'in_progress' => 'En Progreso',
            'completed' => 'Completada',
            'cancelled' => 'Cancelada',
            'no_show' => 'No Asistió',
            'rescheduled' => 'Reprogramada',
            default => $this->status,
        };
    }
}
