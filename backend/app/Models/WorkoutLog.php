<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkoutLog extends Model
{
    protected $fillable = [
        'user_id',
        'workout_id',
        'workout_plan_id',
        'completed_at',
        'duration',
        'notes',
        'exercises_completed',
        'rating',
    ];

    protected $casts = [
        'completed_at' => 'date',
        'exercises_completed' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function workout(): BelongsTo
    {
        return $this->belongsTo(Workout::class);
    }

    public function workoutPlan(): BelongsTo
    {
        return $this->belongsTo(WorkoutPlan::class);
    }
}