<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\Medication;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class MedicalService
{
    public function listRecords(int $userId)
    {
        return MedicalRecord::where('user_id', $userId)
            ->with(['prescribedMedications', 'appointments'])
            ->latest()
            ->get();
    }

    public function createRecord(array $data, User $user)
    {
        $this->validateRecordData($data, true);

        return MedicalRecord::create([
            'user_id' => $user->id,
            'patient_id' => $user->id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'type' => $data['type'],
            'category' => $data['category'],
            'severity' => $data['severity'],
            'status' => $data['status'],
            'diagnosis_code' => $data['diagnosis_code'] ?? null,
            'treatment_plan' => $data['treatment_plan'] ?? null,
            'medications' => $data['medications'] ?? [],
            'symptoms' => $data['symptoms'] ?? [],
            'notes' => $data['notes'] ?? null,
            'follow_up_date' => $data['follow_up_date'] ?? null,
            'is_confidential' => $data['is_confidential'] ?? false,
            'is_emergency' => $data['is_emergency'] ?? false,
            'metadata' => $data['metadata'] ?? [],
        ]);
    }

    public function findRecord(MedicalRecord $medicalRecord)
    {
        return $medicalRecord->load(['prescribedMedications', 'appointments']);
    }

    public function updateRecord(MedicalRecord $medicalRecord, array $data)
    {
        $this->validateRecordData($data, false);
        $medicalRecord->update($data);
        return $medicalRecord->load(['prescribedMedications', 'appointments']);
    }

    public function deleteRecord(MedicalRecord $medicalRecord)
    {
        $medicalRecord->delete();
    }

    public function addMedication(MedicalRecord $medicalRecord, User $user, array $data)
    {
        $this->validateMedicationData($data);

        return Medication::create([
            'user_id' => $user->id,
            'medical_record_id' => $medicalRecord->id,
            'patient_id' => $medicalRecord->patient_id,
            'name' => $data['name'],
            'brand_name' => $data['brand_name'] ?? null,
            'generic_name' => $data['generic_name'] ?? null,
            'dosage' => $data['dosage'] ?? null,
            'frequency' => $data['frequency'],
            'route' => $data['route'],
            'strength' => $data['strength'] ?? null,
            'unit' => $data['unit'] ?? null,
            'quantity' => $data['quantity'] ?? null,
            'refills' => $data['refills'] ?? null,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'] ?? null,
            'time_of_day' => $data['time_of_day'] ?? [],
            'with_food' => $data['with_food'] ?? null,
            'instructions' => $data['instructions'] ?? null,
            'side_effects' => $data['side_effects'] ?? [],
            'contraindications' => $data['contraindications'] ?? [],
            'interactions' => $data['interactions'] ?? [],
            'is_prn' => $data['is_prn'] ?? false,
            'is_controlled' => $data['is_controlled'] ?? false,
            'is_emergency_medication' => $data['is_emergency_medication'] ?? false,
            'pharmacy' => $data['pharmacy'] ?? null,
            'prescription_number' => $data['prescription_number'] ?? null,
        ]);
    }

    public function addAppointment(MedicalRecord $medicalRecord, User $user, array $data)
    {
        $this->validateAppointmentData($data);

        return Appointment::create([
            'user_id' => $user->id,
            'patient_id' => $medicalRecord->patient_id,
            'medical_record_id' => $medicalRecord->id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'type' => $data['type'],
            'category' => $data['category'],
            'status' => $data['status'],
            'priority' => $data['priority'],
            'scheduled_date' => $data['scheduled_date'],
            'scheduled_time' => $data['scheduled_time'],
            'duration' => $data['duration'] ?? 60,
            'location' => $data['location'] ?? null,
            'location_type' => $data['location_type'],
            'virtual_meeting_url' => $data['virtual_meeting_url'] ?? null,
            'virtual_meeting_id' => $data['virtual_meeting_id'] ?? null,
            'notes' => $data['notes'] ?? null,
            'preparation_instructions' => $data['preparation_instructions'] ?? null,
            'is_confidential' => $data['is_confidential'] ?? false,
            'is_emergency' => $data['is_emergency'] ?? false,
        ]);
    }

    public function getMedications(MedicalRecord $medicalRecord)
    {
        return $medicalRecord->prescribedMedications()->get();
    }

    public function getAppointments(MedicalRecord $medicalRecord)
    {
        return $medicalRecord->appointments()
            ->orderBy('appointment_date', 'asc')
            ->get();
    }

    private function validateRecordData(array $data, bool $required = true): void
    {
        $rules = [
            'title' => ($required ? 'required|' : '') . 'string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => ($required ? 'required|' : '') . 'in:diagnosis,treatment,prescription,lab_result,imaging,vaccination,allergy',
            'category' => ($required ? 'required|' : '') . 'in:general,emergency,chronic,acute,preventive',
            'severity' => ($required ? 'required|' : '') . 'in:mild,moderate,severe,critical',
            'status' => ($required ? 'required|' : '') . 'in:active,resolved,chronic,monitoring',
            'diagnosis_code' => 'nullable|string|max:50',
            'treatment_plan' => 'nullable|string|max:2000',
            'medications' => 'nullable|array',
            'symptoms' => 'nullable|array',
            'notes' => 'nullable|string|max:2000',
            'follow_up_date' => 'nullable|date',
            'is_confidential' => 'boolean',
            'is_emergency' => 'boolean',
            'metadata' => 'nullable|array',
        ];

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    private function validateMedicationData(array $data): void
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'brand_name' => 'nullable|string|max:255',
            'generic_name' => 'nullable|string|max:255',
            'dosage' => 'nullable|string|max:255',
            'frequency' => 'required|in:once_daily,twice_daily,three_times_daily,four_times_daily,weekly,monthly,as_needed',
            'route' => 'required|in:oral,intravenous,topical,inhalation,rectal,sublingual,buccal',
            'strength' => 'nullable|string|max:100',
            'unit' => 'nullable|in:mg,ml,mcg,g,iu',
            'quantity' => 'nullable|integer|min:0',
            'refills' => 'nullable|integer|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'time_of_day' => 'nullable|array',
            'with_food' => 'nullable|in:before,after,with,without',
            'instructions' => 'nullable|string|max:2000',
            'side_effects' => 'nullable|array',
            'contraindications' => 'nullable|array',
            'interactions' => 'nullable|array',
            'is_prn' => 'boolean',
            'is_controlled' => 'boolean',
            'is_emergency_medication' => 'boolean',
            'pharmacy' => 'nullable|string|max:255',
            'prescription_number' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    private function validateAppointmentData(array $data): void
    {
        $validator = Validator::make($data, [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:consultation,follow_up,emergency,surgery,therapy,lab_test,imaging',
            'category' => 'required|in:general,specialized,urgent,routine',
            'status' => 'required|in:scheduled,confirmed,in_progress,completed,cancelled,no_show,rescheduled',
            'priority' => 'required|in:low,medium,high,urgent',
            'scheduled_date' => 'required|date|after:today',
            'scheduled_time' => 'required|date',
            'duration' => 'nullable|integer|min:15|max:480',
            'location' => 'nullable|string|max:500',
            'location_type' => 'required|in:physical,virtual,home_visit',
            'virtual_meeting_url' => 'nullable|url',
            'virtual_meeting_id' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:2000',
            'preparation_instructions' => 'nullable|string|max:2000',
            'is_confidential' => 'boolean',
            'is_emergency' => 'boolean',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
