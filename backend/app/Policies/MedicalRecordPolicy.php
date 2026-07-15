<?php

namespace App\Policies;

use App\Models\MedicalRecord;
use App\Models\User;

class MedicalRecordPolicy
{
    public function view(User $user, MedicalRecord $medicalRecord): bool
    {
        return $medicalRecord->user_id === $user->id
            || $medicalRecord->patient_id === $user->id
            || $medicalRecord->doctor_id === $user->id;
    }

    public function update(User $user, MedicalRecord $medicalRecord): bool
    {
        return $medicalRecord->user_id === $user->id
            || $medicalRecord->doctor_id === $user->id;
    }

    public function delete(User $user, MedicalRecord $medicalRecord): bool
    {
        return $medicalRecord->user_id === $user->id;
    }
}
