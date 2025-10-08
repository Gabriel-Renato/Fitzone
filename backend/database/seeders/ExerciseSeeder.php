<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Exercise;

class ExerciseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $exercises = [
            // PEITO
            [
                'name' => 'Supino Reto',
                'description' => 'Exercício básico para desenvolvimento do peitoral',
                'muscle_group' => 'Peito',
                'equipment' => 'Barra/Halteres',
            ],
            [
                'name' => 'Supino Inclinado',
                'description' => 'Foca na parte superior do peitoral',
                'muscle_group' => 'Peito',
                'equipment' => 'Barra/Halteres',
            ],
            [
                'name' => 'Supino Declinado',
                'description' => 'Foca na parte inferior do peitoral',
                'muscle_group' => 'Peito',
                'equipment' => 'Barra/Halteres',
            ],
            [
                'name' => 'Crucifixo',
                'description' => 'Exercício de isolamento para peitoral',
                'muscle_group' => 'Peito',
                'equipment' => 'Halteres/Cabos',
            ],
            [
                'name' => 'Flexão de Braço',
                'description' => 'Exercício com peso corporal para peitoral',
                'muscle_group' => 'Peito',
                'equipment' => 'Peso Corporal',
            ],

            // COSTAS
            [
                'name' => 'Barra Fixa',
                'description' => 'Exercício composto para desenvolvimento das costas',
                'muscle_group' => 'Costas',
                'equipment' => 'Barra Fixa',
            ],
            [
                'name' => 'Remada Curvada',
                'description' => 'Exercício para espessura das costas',
                'muscle_group' => 'Costas',
                'equipment' => 'Barra/Halteres',
            ],
            [
                'name' => 'Remada Cavalinho',
                'description' => 'Exercício para região lombar e dorsais',
                'muscle_group' => 'Costas',
                'equipment' => 'Máquina',
            ],
            [
                'name' => 'Pulldown',
                'description' => 'Exercício para largura das costas',
                'muscle_group' => 'Costas',
                'equipment' => 'Cabo',
            ],
            [
                'name' => 'Levantamento Terra',
                'description' => 'Exercício composto para costas e posterior',
                'muscle_group' => 'Costas',
                'equipment' => 'Barra',
            ],

            // PERNAS
            [
                'name' => 'Agachamento',
                'description' => 'Exercício base para desenvolvimento de pernas',
                'muscle_group' => 'Pernas',
                'equipment' => 'Barra/Livre',
            ],
            [
                'name' => 'Leg Press',
                'description' => 'Exercício para quadríceps e glúteos',
                'muscle_group' => 'Pernas',
                'equipment' => 'Máquina',
            ],
            [
                'name' => 'Cadeira Extensora',
                'description' => 'Isolamento de quadríceps',
                'muscle_group' => 'Pernas',
                'equipment' => 'Máquina',
            ],
            [
                'name' => 'Cadeira Flexora',
                'description' => 'Isolamento de posterior de coxa',
                'muscle_group' => 'Pernas',
                'equipment' => 'Máquina',
            ],
            [
                'name' => 'Stiff',
                'description' => 'Exercício para posterior de coxa e glúteos',
                'muscle_group' => 'Pernas',
                'equipment' => 'Barra/Halteres',
            ],
            [
                'name' => 'Panturrilha em Pé',
                'description' => 'Exercício para panturrilhas',
                'muscle_group' => 'Pernas',
                'equipment' => 'Máquina/Livre',
            ],

            // OMBROS
            [
                'name' => 'Desenvolvimento com Barra',
                'description' => 'Exercício composto para ombros',
                'muscle_group' => 'Ombros',
                'equipment' => 'Barra',
            ],
            [
                'name' => 'Desenvolvimento com Halteres',
                'description' => 'Exercício para ombros com amplitude maior',
                'muscle_group' => 'Ombros',
                'equipment' => 'Halteres',
            ],
            [
                'name' => 'Elevação Lateral',
                'description' => 'Isolamento para deltoide lateral',
                'muscle_group' => 'Ombros',
                'equipment' => 'Halteres/Cabos',
            ],
            [
                'name' => 'Elevação Frontal',
                'description' => 'Isolamento para deltoide anterior',
                'muscle_group' => 'Ombros',
                'equipment' => 'Halteres/Barra',
            ],
            [
                'name' => 'Remada Alta',
                'description' => 'Exercício para trapézio e ombros',
                'muscle_group' => 'Ombros',
                'equipment' => 'Barra/Halteres',
            ],

            // BÍCEPS
            [
                'name' => 'Rosca Direta',
                'description' => 'Exercício básico para bíceps',
                'muscle_group' => 'Bíceps',
                'equipment' => 'Barra/Halteres',
            ],
            [
                'name' => 'Rosca Alternada',
                'description' => 'Exercício unilateral para bíceps',
                'muscle_group' => 'Bíceps',
                'equipment' => 'Halteres',
            ],
            [
                'name' => 'Rosca Scott',
                'description' => 'Exercício isolado para bíceps',
                'muscle_group' => 'Bíceps',
                'equipment' => 'Barra/Halteres',
            ],
            [
                'name' => 'Rosca Martelo',
                'description' => 'Exercício para bíceps e antebraço',
                'muscle_group' => 'Bíceps',
                'equipment' => 'Halteres',
            ],

            // TRÍCEPS
            [
                'name' => 'Tríceps Testa',
                'description' => 'Exercício para massa de tríceps',
                'muscle_group' => 'Tríceps',
                'equipment' => 'Barra/Halteres',
            ],
            [
                'name' => 'Tríceps Pulley',
                'description' => 'Exercício para definição de tríceps',
                'muscle_group' => 'Tríceps',
                'equipment' => 'Cabo',
            ],
            [
                'name' => 'Tríceps Francês',
                'description' => 'Exercício para alongamento do tríceps',
                'muscle_group' => 'Tríceps',
                'equipment' => 'Halteres/Barra',
            ],
            [
                'name' => 'Mergulho',
                'description' => 'Exercício com peso corporal para tríceps',
                'muscle_group' => 'Tríceps',
                'equipment' => 'Paralelas',
            ],

            // ABDÔMEN
            [
                'name' => 'Abdominal Supra',
                'description' => 'Exercício para parte superior do abdômen',
                'muscle_group' => 'Abdômen',
                'equipment' => 'Peso Corporal',
            ],
            [
                'name' => 'Abdominal Infra',
                'description' => 'Exercício para parte inferior do abdômen',
                'muscle_group' => 'Abdômen',
                'equipment' => 'Peso Corporal',
            ],
            [
                'name' => 'Prancha',
                'description' => 'Exercício isométrico para core',
                'muscle_group' => 'Abdômen',
                'equipment' => 'Peso Corporal',
            ],
            [
                'name' => 'Abdominal Oblíquo',
                'description' => 'Exercício para oblíquos',
                'muscle_group' => 'Abdômen',
                'equipment' => 'Peso Corporal',
            ],
        ];

        foreach ($exercises as $exercise) {
            Exercise::create($exercise);
        }
    }
}