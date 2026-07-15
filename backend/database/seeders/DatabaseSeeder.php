<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ProfileSeeder::class,
            SignLanguageSeeder::class,
            ConversationSeeder::class,
            MessageSeeder::class,
            EmergencySeeder::class,
        ]);
    }
}
