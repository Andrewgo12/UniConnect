<?php

namespace Database\Factories;

use App\Models\Emergency;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Emergency>
 */
class EmergencyFactory extends Factory
{
    protected $model = Emergency::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => $this->faker->randomElement(['medical', 'security', 'help']),
            'description' => $this->faker->paragraph(),
            'location' => $this->faker->streetAddress(),
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
            'severity' => $this->faker->randomElement(['low', 'medium', 'high', 'critical']),
            'status' => 'active',
            'resolved_at' => null,
        ];
    }
}
