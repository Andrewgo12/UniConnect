<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Database\Seeder;

class ConversationSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        if ($users->count() < 2) {
            return;
        }

        $admin = $users->firstWhere('email', 'admin@uniconnect.com') ?? $users->first();
        $blind = $users->firstWhere('email', 'blind@uniconnect.com') ?? $users->skip(1)->first();
        $deaf  = $users->firstWhere('email', 'deaf@uniconnect.com')  ?? $users->skip(2)->first();
        $mute  = $users->firstWhere('email', 'mute@uniconnect.com')  ?? $users->skip(3)->first();

        $conversations = [
            [
                'title'      => 'Soporte General',
                'type'       => 'text',
                'status'     => 'active',
                'created_by' => $admin->id,
                'metadata'   => ['category' => 'general'],
                'settings'   => ['allow_file_sharing' => true],
                'participants' => [$admin->id, $blind->id],
            ],
            [
                'title'      => 'Emergencias',
                'type'       => 'text',
                'status'     => 'active',
                'created_by' => $admin->id,
                'metadata'   => ['category' => 'emergency'],
                'settings'   => ['allow_file_sharing' => false],
                'participants' => array_filter([$admin->id, $deaf?->id, $mute?->id]),
            ],
            [
                'title'      => 'Comunicación Accesible',
                'type'       => 'text',
                'status'     => 'active',
                'created_by' => $blind->id,
                'metadata'   => ['category' => 'accessibility'],
                'settings'   => ['allow_voice_messages' => true],
                'participants' => array_filter([$blind->id, $mute?->id]),
            ],
        ];

        foreach ($conversations as $data) {
            $participants = $data['participants'];
            unset($data['participants']);

            $conversation = Conversation::firstOrCreate(
                ['title' => $data['title'], 'created_by' => $data['created_by']],
                $data
            );

            foreach (array_unique(array_filter($participants)) as $userId) {
                if (!$conversation->participants()->where('user_id', $userId)->exists()) {
                    $conversation->participants()->attach($userId, [
                        'role'      => $userId === $data['created_by'] ? 'admin' : 'member',
                        'joined_at' => now(),
                    ]);
                }
            }
        }
    }
}
