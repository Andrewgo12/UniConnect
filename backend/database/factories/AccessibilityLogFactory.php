<?php

namespace Database\Factories;

use App\Models\AccessibilityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AccessibilityLog>
 */
class AccessibilityLogFactory extends Factory
{
    protected $model = AccessibilityLog::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'loggable_type' => null,
            'loggable_id' => null,
            'action' => $this->faker->randomElement(['screen_reader_used', 'voice_command', 'high_contrast_enabled']),
            'feature' => $this->faker->randomElement(['text_to_speech', 'sign_language', 'large_text']),
            'accessibility_mode' => $this->faker->randomElement(['standard', 'screen_reader', 'voice_control']),
            'device_type' => $this->faker->randomElement(['mobile', 'desktop', 'tablet']),
            'input_method' => $this->faker->randomElement(['touch', 'keyboard', 'voice']),
            'assistive_technology' => $this->faker->randomElement(['screen_reader', 'voice_recognizer']),
            'duration' => $this->faker->numberBetween(1, 120),
            'success' => true,
            'error_message' => null,
            'context' => [],
            'previous_mode' => null,
            'new_mode' => null,
            'metadata' => [],
        ];
    }
}
