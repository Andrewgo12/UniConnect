<?php

namespace Database\Factories;

use App\Models\Image;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Image>
 */
class ImageFactory extends Factory
{
    protected $model = Image::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'type' => $this->faker->randomElement(['profile', 'sign_language', 'emergency', 'medical', 'general']),
            'file_path' => $this->faker->filePath(),
            'original_name' => $this->faker->word() . '.jpg',
            'mime_type' => 'image/jpeg',
            'size' => $this->faker->numberBetween(1000, 1000000),
            'width' => $this->faker->numberBetween(100, 1920),
            'height' => $this->faker->numberBetween(100, 1080),
            'alt_text' => $this->faker->sentence(),
            'tags' => $this->faker->words(3),
            'is_public' => true,
            'is_approved' => true,
            'usage_count' => 0,
            'metadata' => [],
        ];
    }
}
