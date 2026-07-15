<?php

namespace Database\Factories;

use App\Models\MedicalRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MedicalRecord>
 */
class MedicalRecordFactory extends Factory
{
    protected $model = MedicalRecord::class;

    public function definition(): array
    {
        $types = ['diagnosis', 'treatment', 'prescription', 'lab_result', 'imaging', 'vaccination', 'allergy'];
        $categories = ['general', 'emergency', 'chronic', 'acute', 'preventive'];
        $severities = ['mild', 'moderate', 'severe', 'critical'];
        $statuses = ['active', 'resolved', 'chronic', 'monitoring'];

        return [
            'user_id' => User::factory(),
            'patient_id' => User::factory(),
            'doctor_id' => User::factory(),
            'title' => $this->faker->sentence(6),
            'description' => $this->faker->paragraph(2),
            'type' => $this->faker->randomElement($types),
            'category' => $this->faker->randomElement($categories),
            'severity' => $this->faker->randomElement($severities),
            'status' => $this->faker->randomElement($statuses),
            'diagnosis_code' => strtoupper($this->faker->bothify('??###')),
            'treatment_plan' => $this->faker->sentence(8),
            'medications' => [],
            'symptoms' => [],
            'notes' => $this->faker->paragraph(),
            'follow_up_date' => $this->faker->dateTimeBetween('now', '+30 days'),
            'is_confidential' => $this->faker->boolean(20),
            'is_emergency' => $this->faker->boolean(10),
            'metadata' => [],
            'diagnosis' => 'Test diagnosis',
        ];
    }
}
