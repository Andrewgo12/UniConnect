<?php

namespace Database\Factories;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Conversation>
 */
class ConversationFactory extends Factory
{
    protected $model = Conversation::class;

    public function definition(): array
    {
        $types = ['individual', 'group', 'support', 'emergency'];
        $statuses = ['active', 'archived', 'closed'];
        $priorities = ['low', 'medium', 'high', 'urgent'];
        $categories = ['general', 'medical', 'emergency', 'accessibility', 'technical'];

        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(2),
            'type' => $this->faker->randomElement($types),
            'status' => $this->faker->randomElement($statuses),
            'priority' => $this->faker->randomElement($priorities),
            'category' => $this->faker->randomElement($categories),
            'settings' => [
                'allow_file_sharing' => true,
                'allow_voice_messages' => true,
                'allow_video_calls' => false,
                'auto_translate' => true,
                'accessibility_mode' => 'standard',
                'max_participants' => 10,
            ],
            'metadata' => [],
            'created_by' => User::factory(),
            'closed_by' => null,
            'closed_at' => null,
            'is_public' => false,
            'is_pinned' => false,
            'is_muted' => false,
        ];
    }
}
