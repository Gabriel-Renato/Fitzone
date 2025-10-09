<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WorkoutLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WorkoutLogController extends Controller
{
    /**
     * Listar histórico de treinos
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Se for personal, pode ver logs dos clientes
        if ($user->isPersonal() && $request->has('client_id')) {
            $clientId = $request->client_id;
            // Verificar se o cliente pertence ao personal
            $client = $user->clientes()->where('users.id', $clientId)->first();
            
            if (!$client) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente não encontrado'
                ], 404);
            }
            
            $logs = WorkoutLog::where('user_id', $clientId)
                ->with('workout.exercises')
                ->orderBy('completed_at', 'desc')
                ->get();
        } else {
            // Cliente vê apenas seus próprios logs
            $logs = WorkoutLog::where('user_id', $user->id)
                ->with('workout.exercises')
                ->orderBy('completed_at', 'desc')
                ->get();
        }

        return response()->json([
            'success' => true,
            'data' => $logs
        ]);
    }

    /**
     * Registrar treino realizado
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'workout_id' => 'required|exists:workouts,id',
            'workout_plan_id' => 'nullable|exists:workout_plans,id',
            'completed_at' => 'required|date',
            'duration' => 'nullable|integer|min:1',
            'notes' => 'nullable|string',
            'exercises_completed' => 'nullable|array',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $log = WorkoutLog::create([
            'user_id' => $request->user()->id,
            'workout_id' => $request->workout_id,
            'workout_plan_id' => $request->workout_plan_id,
            'completed_at' => $request->completed_at,
            'duration' => $request->duration,
            'notes' => $request->notes,
            'exercises_completed' => $request->exercises_completed,
            'rating' => $request->rating,
        ]);

        $log->load('workout.exercises');

        return response()->json([
            'success' => true,
            'message' => 'Treino registrado com sucesso!',
            'data' => $log
        ], 201);
    }

    /**
     * Detalhes do log
     */
    public function show(Request $request, string $id)
    {
        $user = $request->user();
        $log = WorkoutLog::with('workout.exercises')->findOrFail($id);

        // Verificar permissão
        if ($log->user_id !== $user->id && !$user->isPersonal()) {
            return response()->json([
                'success' => false,
                'message' => 'Acesso negado'
            ], 403);
        }

        // Se for personal, verificar se o cliente pertence a ele
        if ($user->isPersonal() && $log->user_id !== $user->id) {
            $client = $user->clientes()->where('users.id', $log->user_id)->first();
            if (!$client) {
                return response()->json([
                    'success' => false,
                    'message' => 'Acesso negado'
                ], 403);
            }
        }

        return response()->json([
            'success' => true,
            'data' => $log
        ]);
    }

    /**
     * Atualizar log
     */
    public function update(Request $request, string $id)
    {
        $log = WorkoutLog::findOrFail($id);

        // Apenas o próprio cliente pode atualizar seus logs
        if ($log->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Acesso negado'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'duration' => 'nullable|integer|min:1',
            'notes' => 'nullable|string',
            'exercises_completed' => 'nullable|array',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $log->update($request->only(['duration', 'notes', 'exercises_completed', 'rating']));

        return response()->json([
            'success' => true,
            'message' => 'Log atualizado com sucesso',
            'data' => $log
        ]);
    }

    /**
     * Deletar log
     */
    public function destroy(Request $request, string $id)
    {
        $log = WorkoutLog::findOrFail($id);

        if ($log->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Acesso negado'
            ], 403);
        }

        $log->delete();

        return response()->json([
            'success' => true,
            'message' => 'Log deletado com sucesso'
        ]);
    }

    /**
     * Estatísticas
     */
    public function stats(Request $request)
    {
        $user = $request->user();
        
        $userId = $user->id;
        if ($user->isPersonal() && $request->has('client_id')) {
            $userId = $request->client_id;
        }

        $stats = [
            'total_workouts' => WorkoutLog::where('user_id', $userId)->count(),
            'this_month' => WorkoutLog::where('user_id', $userId)
                ->whereMonth('completed_at', now()->month)
                ->count(),
            'this_week' => WorkoutLog::where('user_id', $userId)
                ->whereBetween('completed_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
            'avg_duration' => WorkoutLog::where('user_id', $userId)
                ->avg('duration'),
            'avg_rating' => WorkoutLog::where('user_id', $userId)
                ->avg('rating'),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}