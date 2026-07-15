<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProfileSeeder extends Seeder
{
    public function run(): void
    {
        User::whereDoesntHave('profile')->each(function (User $user) {
            Profile::create([
                'user_id'     => $user->id,
                'name'        => $user->name,
                'blind'       => false,
                'deaf'        => false,
                'mute'        => false,
                'preferences' => ['theme' => 'light', 'language' => 'es-CO'],
            ]);
        });
    }
}
