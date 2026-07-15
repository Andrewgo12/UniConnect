<?php

namespace Database\Factories;

use App\Models\SignLanguage;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SignLanguage>
 */
class SignLanguageFactory extends Factory
{
    protected $model = SignLanguage::class;

    public function definition(): array
    {
        $categories = ['medical', 'general', 'accessibility', 'education'];
        $levels = ['beginner', 'intermediate', 'advanced'];
        $regions = ['colombian', 'international', 'local'];

        return [
            'user_id' => User::factory(),
            'title' => $this->faker->sentence(5),
            'description' => $this->faker->paragraph(2),
            'category' => $this->faker->randomElement($categories),
            'difficulty_level' => $this->faker->randomElement($levels),
            'region' => $this->faker->randomElement($regions),
            'video_url' => $this->faker->url(),
            'image_url' => $this->faker->imageUrl(),
            'thumbnail_url' => $this->faker->imageUrl(320, 240),
            'duration' => $this->faker->numberBetween(30, 300),
            'tags' => $this->faker->words(3),
            'is_public' => true,
            'is_approved' => true,
            'usage_count' => 0,
            'metadata' => [],
        ];
    }
}
