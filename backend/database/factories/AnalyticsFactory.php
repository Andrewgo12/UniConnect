<?php

namespace Database\Factories;

use App\Models\Analytics;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Analytics>
 */
class AnalyticsFactory extends Factory
{
    protected $model = Analytics::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'event_type' => $this->faker->randomElement(['user_action', 'system_event', 'medical_event']),
            'category' => $this->faker->randomElement(['usage', 'performance', 'security']),
            'action' => $this->faker->randomElement(['login', 'logout', 'message_sent']),
            'resource_type' => $this->faker->randomElement(['user', 'message', 'conversation']),
            'resource_id' => $this->faker->optional()->numberBetween(1, 1000),
            'value' => $this->faker->randomFloat(2, 0, 100),
            'metadata' => [],
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
            'session_id' => $this->faker->uuid(),
            'device_type' => $this->faker->randomElement(['mobile', 'desktop', 'tablet']),
            'platform' => $this->faker->randomElement(['web', 'mobile_app', 'api']),
            'language' => 'es-CO',
            'accessibility_mode' => 'standard',
            'response_time' => $this->faker->numberBetween(10, 500),
            'error_code' => null,
            'success' => true,
            'location' => null,
            'timezone' => 'UTC',
        ];
    }
}
