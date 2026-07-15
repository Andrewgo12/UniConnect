<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\User;
use App\Models\MedicalRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Appointment>
 */
class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'patient_id' => User::factory(),
            'doctor_id' => User::factory(),
            'medical_record_id' => MedicalRecord::factory(),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'type' => $this->faker->randomElement(['consultation', 'follow_up', 'emergency', 'surgery', 'therapy']),
            'category' => $this->faker->randomElement(['general', 'specialized', 'urgent', 'routine']),
            'status' => $this->faker->randomElement(['scheduled', 'confirmed', 'in_progress', 'completed', 'cancelled']),
            'priority' => $this->faker->randomElement(['low', 'medium', 'high', 'urgent']),
            'scheduled_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'scheduled_time' => $this->faker->dateTimeBetween('now', '+1 month'),
            'duration' => $this->faker->numberBetween(15, 120),
            'location' => $this->faker->address(),
            'location_type' => $this->faker->randomElement(['physical', 'virtual', 'home_visit']),
            'virtual_meeting_url' => $this->faker->url(),
            'virtual_meeting_id' => $this->faker->uuid(),
            'notes' => $this->faker->paragraph(),
            'preparation_instructions' => $this->faker->paragraph(),
            'cancellation_reason' => null,
            'rescheduling_notes' => null,
            'reminder_sent' => false,
            'reminder_sent_at' => null,
            'is_confidential' => $this->faker->boolean(),
            'is_emergency' => $this->faker->boolean(),
            'metadata' => [],
        ];
    }
}
