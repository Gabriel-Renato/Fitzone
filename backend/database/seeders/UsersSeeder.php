<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\PersonalClient;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar Personal Trainer
        $personal = User::create([
            'name' => 'Carlos Personal',
            'email' => 'personal@fitzone.com',
            'password' => Hash::make('password'),
            'role' => 'personal',
            'phone' => '(11) 98765-4321',
            'bio' => 'Personal Trainer especializado em hipertrofia e emagrecimento',
        ]);

        // Criar Clientes
        $cliente1 = User::create([
            'name' => 'João Silva',
            'email' => 'joao@fitzone.com',
            'password' => Hash::make('password'),
            'role' => 'cliente',
            'phone' => '(11) 91234-5678',
            'personal_id' => $personal->id,
        ]);

        $cliente2 = User::create([
            'name' => 'Maria Santos',
            'email' => 'maria@fitzone.com',
            'password' => Hash::make('password'),
            'role' => 'cliente',
            'phone' => '(11) 98888-7777',
            'personal_id' => $personal->id,
        ]);

        // Criar relacionamentos Personal-Cliente
        PersonalClient::create([
            'personal_id' => $personal->id,
            'client_id' => $cliente1->id,
            'status' => 'ativo',
            'goals' => 'Ganhar massa muscular e definir abdômen',
            'observations' => 'Cliente iniciante, foco em técnica',
        ]);

        PersonalClient::create([
            'personal_id' => $personal->id,
            'client_id' => $cliente2->id,
            'status' => 'ativo',
            'goals' => 'Emagrecimento e condicionamento físico',
            'observations' => 'Cliente intermediário, aumentar intensidade gradualmente',
        ]);

        echo "✅ Usuários criados:\n";
        echo "   Personal: personal@fitzone.com / password\n";
        echo "   Cliente 1: joao@fitzone.com / password\n";
        echo "   Cliente 2: maria@fitzone.com / password\n";
    }
}