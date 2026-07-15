<?php

namespace Database\Factories;

use App\Models\Message;
use App\Models\User;
use App\Models\Conversation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Message>
 */
class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition(): array
    {
        return [
            'conversation_id' => Conversation::factory(),
            'user_id' => User::factory(),
            'content' => $this->faker->paragraph(),
            'type' => $this->faker->randomElement(['text', 'voice', 'video', 'image', 'file', 'sign_language', 'emergency']),
            'status' => $this->faker->randomElement(['sent', 'delivered', 'read', 'failed']),
            'priority' => $this->faker->randomElement(['low', 'medium', 'high', 'urgent']),
            'metadata' => [],
            'parent_id' => null,
            'edited_at' => null,
            'deleted_at' => null,
            'is_edited' => false,
            'is_deleted' => false,
            'is_pinned' => false,
            'accessibility_data' => [],
        ];
    }
}
