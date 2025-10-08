<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ExerciseController;
use App\Http\Controllers\Api\WorkoutController;
use App\Http\Controllers\Api\WorkoutPlanController;

// Rotas públicas (sem autenticação por enquanto)
Route::prefix('v1')->group(function () {
    
    // Exercises
    Route::apiResource('exercises', ExerciseController::class);
    Route::get('exercises/muscle-groups', [ExerciseController::class, 'muscleGroups']);
    
    // Workouts
    Route::apiResource('workouts', WorkoutController::class);
    
    // Workout Plans
    Route::apiResource('workout-plans', WorkoutPlanController::class);
    Route::get('workout-plans/day/{day}', [WorkoutPlanController::class, 'byDay']);
});

// Rotas protegidas (para implementação futura)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
