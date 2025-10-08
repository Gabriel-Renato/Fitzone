<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkoutPlan extends Model
{
    protected $fillable = [
        'user_id',
        'workout_id',
        'day_of_week',
        'scheduled_time',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'scheduled_time' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function workout(): BelongsTo
    {
        return $this->belongsTo(Workout::class);
    }
}
