<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Workout;
use App\Models\WorkoutExercise;
use App\Models\Exercise;
use App\Models\User;

class WorkoutsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar o personal
        $personal = User::where('role', 'personal')->first();
        
        if (!$personal) {
            echo "❌ Personal não encontrado. Execute UsersSeeder primeiro.\n";
            return;
        }

        // Buscar exercícios por grupo muscular
        $exerciciosPeito = Exercise::where('muscle_group', 'Peito')->take(4)->get();
        $exerciciosTriceps = Exercise::where('muscle_group', 'Tríceps')->take(3)->get();
        $exerciciosCostas = Exercise::where('muscle_group', 'Costas')->take(4)->get();
        $exerciciosBiceps = Exercise::where('muscle_group', 'Bíceps')->take(3)->get();
        $exerciciosPernas = Exercise::where('muscle_group', 'Pernas')->take(5)->get();
        $exerciciosOmbros = Exercise::where('muscle_group', 'Ombros')->take(4)->get();
        $exerciciosAbdomen = Exercise::where('muscle_group', 'Abdômen')->take(3)->get();

        // Treino A - Peito e Tríceps
        $treinoA = Workout::create([
            'user_id' => $personal->id,
            'name' => 'Treino A - Peito e Tríceps',
            'description' => 'Foco em desenvolvimento de peitoral e tríceps',
            'focus' => 'Hipertrofia',
        ]);

        $order = 1;
        foreach ($exerciciosPeito as $ex) {
            WorkoutExercise::create([
                'workout_id' => $treinoA->id,
                'exercise_id' => $ex->id,
                'order' => $order++,
                'sets' => 4,
                'reps' => '8-12',
                'weight' => null,
                'rest_time' => 90,
            ]);
        }
        foreach ($exerciciosTriceps as $ex) {
            WorkoutExercise::create([
                'workout_id' => $treinoA->id,
                'exercise_id' => $ex->id,
                'order' => $order++,
                'sets' => 3,
                'reps' => '10-15',
                'weight' => null,
                'rest_time' => 60,
            ]);
        }

        // Treino B - Costas e Bíceps
        $treinoB = Workout::create([
            'user_id' => $personal->id,
            'name' => 'Treino B - Costas e Bíceps',
            'description' => 'Foco em desenvolvimento de costas e bíceps',
            'focus' => 'Hipertrofia',
        ]);

        $order = 1;
        foreach ($exerciciosCostas as $ex) {
            WorkoutExercise::create([
                'workout_id' => $treinoB->id,
                'exercise_id' => $ex->id,
                'order' => $order++,
                'sets' => 4,
                'reps' => '8-12',
                'weight' => null,
                'rest_time' => 90,
            ]);
        }
        foreach ($exerciciosBiceps as $ex) {
            WorkoutExercise::create([
                'workout_id' => $treinoB->id,
                'exercise_id' => $ex->id,
                'order' => $order++,
                'sets' => 3,
                'reps' => '10-12',
                'weight' => null,
                'rest_time' => 60,
            ]);
        }

        // Treino C - Pernas
        $treinoC = Workout::create([
            'user_id' => $personal->id,
            'name' => 'Treino C - Pernas Completo',
            'description' => 'Treino completo de pernas e glúteos',
            'focus' => 'Hipertrofia',
        ]);

        $order = 1;
        foreach ($exerciciosPernas as $ex) {
            WorkoutExercise::create([
                'workout_id' => $treinoC->id,
                'exercise_id' => $ex->id,
                'order' => $order++,
                'sets' => 4,
                'reps' => '10-15',
                'weight' => null,
                'rest_time' => 90,
            ]);
        }

        // Treino D - Ombros e Abdômen
        $treinoD = Workout::create([
            'user_id' => $personal->id,
            'name' => 'Treino D - Ombros e Abdômen',
            'description' => 'Foco em ombros e core',
            'focus' => 'Definição',
        ]);

        $order = 1;
        foreach ($exerciciosOmbros as $ex) {
            WorkoutExercise::create([
                'workout_id' => $treinoD->id,
                'exercise_id' => $ex->id,
                'order' => $order++,
                'sets' => 3,
                'reps' => '12-15',
                'weight' => null,
                'rest_time' => 60,
            ]);
        }
        foreach ($exerciciosAbdomen as $ex) {
            WorkoutExercise::create([
                'workout_id' => $treinoD->id,
                'exercise_id' => $ex->id,
                'order' => $order++,
                'sets' => 3,
                'reps' => '15-20',
                'weight' => null,
                'rest_time' => 45,
            ]);
        }

        echo "✅ 4 treinos criados com sucesso:\n";
        echo "   - Treino A: Peito e Tríceps\n";
        echo "   - Treino B: Costas e Bíceps\n";
        echo "   - Treino C: Pernas Completo\n";
        echo "   - Treino D: Ombros e Abdômen\n";
    }
}