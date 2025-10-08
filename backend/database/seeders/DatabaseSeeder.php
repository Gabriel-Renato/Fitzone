<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Criar usuário padrão
        User::factory()->create([
            'name' => 'FitZone User',
            'email' => 'user@fitzone.com',
        ]);

        // Popular exercícios
        $this->call([
            ExerciseSeeder::class,
        ]);
    }
}
