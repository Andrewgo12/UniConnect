<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    public function run(): void
    {
        $conversations = Conversation::with('participants')->get();

        if ($conversations->isEmpty()) {
            return;
        }

        $samples = [
            ['content' => 'Hola, ¿cómo puedo ayudarte?',          'type' => 'text',      'priority' => 'medium'],
            ['content' => 'Necesito asistencia con accesibilidad', 'type' => 'text',      'priority' => 'medium'],
            ['content' => 'Sí',                                    'type' => 'phrase',    'priority' => 'low'],
            ['content' => 'No',                                    'type' => 'phrase',    'priority' => 'low'],
            ['content' => 'Ayuda',                                 'type' => 'phrase',    'priority' => 'high'],
            ['content' => 'Gracias por tu apoyo',                  'type' => 'text',      'priority' => 'low'],
            ['content' => 'Dolor',                                 'type' => 'phrase',    'priority' => 'high'],
            ['content' => 'Agua',                                  'type' => 'phrase',    'priority' => 'medium'],
        ];

        foreach ($conversations as $conversation) {
            $participants = $conversation->participants;

            if ($participants->isEmpty()) {
                continue;
            }

            foreach (array_slice($samples, 0, 4) as $i => $sample) {
                $sender = $participants->get($i % $participants->count());

                Message::firstOrCreate(
                    [
                        'conversation_id' => $conversation->id,
                        'user_id'         => $sender->id,
                        'content'         => $sample['content'],
                    ],
                    [
                        'type'       => $sample['type'],
                        'priority'   => $sample['priority'],
                        'status'     => 'read',
                        'is_deleted' => false,
                        'is_edited'  => false,
                        'is_pinned'  => false,
                        'metadata'   => ['language' => 'es-CO', 'accessibility_mode' => 'standard'],
                    ]
                );
            }
        }
    }
}
