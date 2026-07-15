<?php

namespace Database\Factories;

use App\Models\Audio;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Audio>
 */
class AudioFactory extends Factory
{
    protected $model = Audio::class;

    public function configure(): static
    {
        return $this->afterMaking(function (Audio $audio): void {
            if ($audio->type === 'emergency') {
                $audio->is_processed = true;
            }
        });
    }

    public function definition(): array
    {
        $type = $this->faker->randomElement(['speech', 'voice_note', 'sign_language']);

        return [
            'user_id' => User::factory(),
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'type' => $type,
            'file_path' => $this->faker->filePath(),
            'original_name' => $this->faker->word().'.mp3',
            'mime_type' => 'audio/mpeg',
            'size' => $this->faker->numberBetween(1000, 5_000_000),
            'duration' => $this->faker->numberBetween(5, 300),
            'transcript' => null,
            'language' => 'es-CO',
            'quality' => $this->faker->randomElement(['low', 'medium', 'high']),
            'is_public' => false,
            'is_processed' => false,
            'metadata' => [],
        ];
    }
}
