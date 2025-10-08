<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Workout;
use App\Models\WorkoutExercise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class WorkoutController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $userId = $request->user_id ?? 1; // Temporário até implementar autenticação
        
        $workouts = Workout::where('user_id', $userId)
            ->with('exercises')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $workouts
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'focus' => 'required|string|max:255',
            'exercises' => 'required|array',
            'exercises.*.exercise_id' => 'required|exists:exercises,id',
            'exercises.*.order' => 'required|integer',
            'exercises.*.sets' => 'required|integer|min:1',
            'exercises.*.reps' => 'required|string',
            'exercises.*.weight' => 'nullable|numeric|min:0',
            'exercises.*.rest_time' => 'nullable|integer|min:0',
            'exercises.*.notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $workout = Workout::create([
                'user_id' => $request->user_id,
                'name' => $request->name,
                'description' => $request->description,
                'focus' => $request->focus,
            ]);

            foreach ($request->exercises as $exercise) {
                WorkoutExercise::create([
                    'workout_id' => $workout->id,
                    'exercise_id' => $exercise['exercise_id'],
                    'order' => $exercise['order'],
                    'sets' => $exercise['sets'],
                    'reps' => $exercise['reps'],
                    'weight' => $exercise['weight'] ?? null,
                    'rest_time' => $exercise['rest_time'] ?? null,
                    'notes' => $exercise['notes'] ?? null,
                ]);
            }

            DB::commit();

            $workout->load('exercises');

            return response()->json([
                'success' => true,
                'message' => 'Treino criado com sucesso',
                'data' => $workout
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar treino: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $workout = Workout::with('exercises')->find($id);

        if (!$workout) {
            return response()->json([
                'success' => false,
                'message' => 'Treino não encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $workout
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $workout = Workout::find($id);

        if (!$workout) {
            return response()->json([
                'success' => false,
                'message' => 'Treino não encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'focus' => 'sometimes|required|string|max:255',
            'exercises' => 'sometimes|required|array',
            'exercises.*.exercise_id' => 'required|exists:exercises,id',
            'exercises.*.order' => 'required|integer',
            'exercises.*.sets' => 'required|integer|min:1',
            'exercises.*.reps' => 'required|string',
            'exercises.*.weight' => 'nullable|numeric|min:0',
            'exercises.*.rest_time' => 'nullable|integer|min:0',
            'exercises.*.notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $workout->update($request->only(['name', 'description', 'focus']));

            if ($request->has('exercises')) {
                // Remove exercícios antigos
                WorkoutExercise::where('workout_id', $workout->id)->delete();

                // Adiciona novos exercícios
                foreach ($request->exercises as $exercise) {
                    WorkoutExercise::create([
                        'workout_id' => $workout->id,
                        'exercise_id' => $exercise['exercise_id'],
                        'order' => $exercise['order'],
                        'sets' => $exercise['sets'],
                        'reps' => $exercise['reps'],
                        'weight' => $exercise['weight'] ?? null,
                        'rest_time' => $exercise['rest_time'] ?? null,
                        'notes' => $exercise['notes'] ?? null,
                    ]);
                }
            }

            DB::commit();

            $workout->load('exercises');

            return response()->json([
                'success' => true,
                'message' => 'Treino atualizado com sucesso',
                'data' => $workout
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar treino: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $workout = Workout::find($id);

        if (!$workout) {
            return response()->json([
                'success' => false,
                'message' => 'Treino não encontrado'
            ], 404);
        }

        $workout->delete();

        return response()->json([
            'success' => true,
            'message' => 'Treino deletado com sucesso'
        ]);
    }
}