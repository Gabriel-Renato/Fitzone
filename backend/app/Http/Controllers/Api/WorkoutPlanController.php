<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WorkoutPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WorkoutPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $userId = $request->user_id ?? 1; // Temporário até implementar autenticação
        
        $workoutPlans = WorkoutPlan::where('user_id', $userId)
            ->with('workout.exercises')
            ->orderBy('day_of_week')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $workoutPlans
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'workout_id' => 'required|exists:workouts,id',
            'day_of_week' => 'required|in:Segunda,Terça,Quarta,Quinta,Sexta,Sábado,Domingo',
            'scheduled_time' => 'nullable|date_format:H:i',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $workoutPlan = WorkoutPlan::create($request->all());
        $workoutPlan->load('workout.exercises');

        return response()->json([
            'success' => true,
            'message' => 'Plano de treino criado com sucesso',
            'data' => $workoutPlan
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $workoutPlan = WorkoutPlan::with('workout.exercises')->find($id);

        if (!$workoutPlan) {
            return response()->json([
                'success' => false,
                'message' => 'Plano de treino não encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $workoutPlan
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $workoutPlan = WorkoutPlan::find($id);

        if (!$workoutPlan) {
            return response()->json([
                'success' => false,
                'message' => 'Plano de treino não encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'workout_id' => 'sometimes|required|exists:workouts,id',
            'day_of_week' => 'sometimes|required|in:Segunda,Terça,Quarta,Quinta,Sexta,Sábado,Domingo',
            'scheduled_time' => 'nullable|date_format:H:i',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $workoutPlan->update($request->all());
        $workoutPlan->load('workout.exercises');

        return response()->json([
            'success' => true,
            'message' => 'Plano de treino atualizado com sucesso',
            'data' => $workoutPlan
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $workoutPlan = WorkoutPlan::find($id);

        if (!$workoutPlan) {
            return response()->json([
                'success' => false,
                'message' => 'Plano de treino não encontrado'
            ], 404);
        }

        $workoutPlan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Plano de treino deletado com sucesso'
        ]);
    }

    /**
     * Get workout plan by day of week
     */
    public function byDay(Request $request, string $day)
    {
        $userId = $request->user_id ?? 1;
        
        $workoutPlan = WorkoutPlan::where('user_id', $userId)
            ->where('day_of_week', $day)
            ->where('is_active', true)
            ->with('workout.exercises')
            ->first();

        if (!$workoutPlan) {
            return response()->json([
                'success' => false,
                'message' => 'Nenhum treino programado para este dia'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $workoutPlan
        ]);
    }
}