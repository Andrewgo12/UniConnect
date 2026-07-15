<?php

namespace Database\Seeders;

use App\Models\SignLanguage;
use App\Models\User;
use Illuminate\Database\Seeder;

class SignLanguageSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = User::first()?->id ?? 1;

        $signs = [
            // Básicas
            ['title' => 'Hola',       'category' => 'colombian', 'difficulty_level' => 'beginner'],
            ['title' => 'Gracias',    'category' => 'colombian', 'difficulty_level' => 'beginner'],
            ['title' => 'Por favor',  'category' => 'colombian', 'difficulty_level' => 'beginner'],
            ['title' => 'Sí',         'category' => 'colombian', 'difficulty_level' => 'beginner'],
            ['title' => 'No',         'category' => 'colombian', 'difficulty_level' => 'beginner'],
            ['title' => 'Ayuda',      'category' => 'colombian', 'difficulty_level' => 'beginner'],
            ['title' => 'Agua',       'category' => 'colombian', 'difficulty_level' => 'beginner'],
            ['title' => 'Comida',     'category' => 'colombian', 'difficulty_level' => 'beginner'],
            // Emergencias
            ['title' => 'Emergencia', 'category' => 'emergency', 'difficulty_level' => 'beginner'],
            ['title' => 'Médico',     'category' => 'emergency', 'difficulty_level' => 'beginner'],
            ['title' => 'Policía',    'category' => 'emergency', 'difficulty_level' => 'beginner'],
            ['title' => 'Fuego',      'category' => 'emergency', 'difficulty_level' => 'beginner'],
            ['title' => 'Dolor',      'category' => 'emergency', 'difficulty_level' => 'beginner'],
        ];

        foreach ($signs as $sign) {
            SignLanguage::firstOrCreate(
                ['title' => $sign['title'], 'category' => $sign['category']],
                array_merge($sign, [
                    'user_id'    => $adminId,
                    'is_public'  => true,
                    'is_approved'=> true,
                    'region'     => 'colombian',
                    'usage_count'=> 0,
                ])
            );
        }
    }
}
