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
        // Popular usuários (Personal e Clientes)
        $this->call([
            UsersSeeder::class,
            ExerciseSeeder::class,
            WorkoutsSeeder::class,
        ]);
    }
}
