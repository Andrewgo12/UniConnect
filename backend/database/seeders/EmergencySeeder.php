<?php

namespace Database\Seeders;

use App\Models\Emergency;
use App\Models\User;
use Illuminate\Database\Seeder;

class EmergencySeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            return;
        }

        $emergencies = [
            [
                'email'       => 'blind@uniconnect.com',
                'type'        => 'medical',
                'description' => 'Necesito asistencia médica urgente',
                'status'      => 'resolved',
                'location'    => ['lat' => 4.7110, 'lng' => -74.0721],
                'resolved_at' => now()->subDays(2),
            ],
            [
                'email'       => 'deaf@uniconnect.com',
                'type'        => 'help',
                'description' => 'Necesito ayuda para comunicarme',
                'status'      => 'resolved',
                'location'    => ['lat' => 4.6097, 'lng' => -74.0817],
                'resolved_at' => now()->subDay(),
            ],
            [
                'email'       => 'mute@uniconnect.com',
                'type'        => 'medical',
                'description' => 'Alerta de emergencia médica',
                'status'      => 'active',
                'location'    => ['lat' => 4.6534, 'lng' => -74.0836],
                'resolved_at' => null,
            ],
        ];

        foreach ($emergencies as $data) {
            $user = $users->firstWhere('email', $data['email']);

            if (!$user) {
                continue;
            }

            Emergency::firstOrCreate(
                ['user_id' => $user->id, 'type' => $data['type'], 'description' => $data['description']],
                [
                    'status'      => $data['status'],
                    'location'    => $data['location'],
                    'resolved_at' => $data['resolved_at'],
                ]
            );
        }
    }
}
