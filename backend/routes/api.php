<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ExerciseController;
use App\Http\Controllers\Api\WorkoutController;
use App\Http\Controllers\Api\WorkoutPlanController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\WorkoutLogController;

// ============================================
// ROTAS PÚBLICAS (SEM AUTENTICAÇÃO)
// ============================================
Route::prefix('v1')->group(function () {
    
    // Autenticação
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    
});

// ============================================
// ROTAS PROTEGIDAS (COM AUTENTICAÇÃO)
// ============================================
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    
    // Auth
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
    
    // Exercises (todos podem acessar)
    Route::apiResource('exercises', ExerciseController::class);
    Route::get('exercises-muscle-groups', [ExerciseController::class, 'muscleGroups']);
    
    // Workouts
    Route::apiResource('workouts', WorkoutController::class);
    
    // Workout Plans
    Route::apiResource('workout-plans', WorkoutPlanController::class);
    Route::get('workout-plans/day/{day}', [WorkoutPlanController::class, 'byDay']);
    
    // Workout Logs (Histórico de treinos)
    Route::apiResource('workout-logs', WorkoutLogController::class);
    Route::get('workout-logs-stats', [WorkoutLogController::class, 'stats']);
    
    // ============================================
    // ROTAS EXCLUSIVAS PARA PERSONAL
    // ============================================
    Route::middleware('role:personal')->group(function () {
        // Gestão de Clientes
        Route::get('clients', [ClientController::class, 'index']);
        Route::post('clients', [ClientController::class, 'store']);
        Route::get('clients/{id}', [ClientController::class, 'show']);
        Route::put('clients/{id}', [ClientController::class, 'update']);
        Route::delete('clients/{id}', [ClientController::class, 'destroy']);
    });
    
});