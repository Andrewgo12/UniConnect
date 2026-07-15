<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Admin UniConnect', 'email' => 'admin@uniconnect.com', 'blind' => false, 'deaf' => false, 'mute' => false],
            ['name' => 'Usuario Ciego',    'email' => 'blind@uniconnect.com',  'blind' => true,  'deaf' => false, 'mute' => false],
            ['name' => 'Usuario Sordo',    'email' => 'deaf@uniconnect.com',   'blind' => false, 'deaf' => true,  'mute' => false],
            ['name' => 'Usuario Mudo',     'email' => 'mute@uniconnect.com',   'blind' => false, 'deaf' => false, 'mute' => true],
        ];

        foreach ($users as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                ['name' => $data['name'], 'password' => Hash::make('password123')]
            );

            if (!$user->profile) {
                $user->profile()->create([
                    'name'  => $data['name'],
                    'blind' => $data['blind'],
                    'deaf'  => $data['deaf'],
                    'mute'  => $data['mute'],
                    'preferences' => [
                        'theme'    => 'light',
                        'language' => 'es-CO',
                    ],
                ]);
            }
        }
    }
}
