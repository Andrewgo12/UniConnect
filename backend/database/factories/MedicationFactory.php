<?php

namespace Database\Factories;

use App\Models\Medication;
use App\Models\User;
use App\Models\MedicalRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Medication>
 */
class MedicationFactory extends Factory
{
    protected $model = Medication::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'medical_record_id' => MedicalRecord::factory(),
            'patient_id' => User::factory(),
            'doctor_id' => User::factory(),
            'name' => $this->faker->word(),
            'brand_name' => $this->faker->optional()->word(),
            'generic_name' => $this->faker->optional()->word(),
            'dosage' => $this->faker->randomElement(['10mg', '20mg', '50mg', '100mg']),
            'frequency' => $this->faker->randomElement(['daily', 'twice daily', 'weekly']),
            'route' => $this->faker->randomElement(['oral', 'intravenous', 'topical']),
            'strength' => $this->faker->randomElement(['10mg', '20mg', '50mg']),
            'unit' => $this->faker->randomElement(['mg', 'ml', 'units']),
            'quantity' => $this->faker->numberBetween(10, 100),
            'refills' => $this->faker->numberBetween(0, 5),
            'start_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'end_date' => $this->faker->optional()->dateTimeBetween('now', '+1 month'),
            'time_of_day' => ['morning', 'evening'],
            'with_food' => $this->faker->boolean(),
            'instructions' => $this->faker->sentence(),
            'side_effects' => [],
            'contraindications' => [],
            'interactions' => [],
            'status' => $this->faker->randomElement(['active', 'inactive', 'discontinued']),
            'is_prn' => $this->faker->boolean(),
            'is_controlled' => $this->faker->boolean(),
            'is_emergency_medication' => $this->faker->boolean(),
            'pharmacy' => $this->faker->company(),
            'prescription_number' => $this->faker->unique()->numerify('RX########'),
            'metadata' => [],
        ];
    }
}
