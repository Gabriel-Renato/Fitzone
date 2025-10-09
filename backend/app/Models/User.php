<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'personal_id',
        'phone',
        'bio',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Verificar se é personal
    public function isPersonal(): bool
    {
        return $this->role === 'personal';
    }

    // Verificar se é cliente
    public function isCliente(): bool
    {
        return $this->role === 'cliente';
    }

    // Personal trainer do cliente
    public function personal(): BelongsTo
    {
        return $this->belongsTo(User::class, 'personal_id');
    }

    // Clientes do personal (se for personal)
    public function clientes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'personal_clients', 'personal_id', 'client_id')
            ->withPivot('status', 'start_date', 'end_date', 'goals', 'observations')
            ->withTimestamps();
    }

    // Personals que este usuário teve (se for cliente)
    public function personals(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'personal_clients', 'client_id', 'personal_id')
            ->withPivot('status', 'start_date', 'end_date', 'goals', 'observations')
            ->withTimestamps();
    }

    // Treinos criados
    public function workouts(): HasMany
    {
        return $this->hasMany(Workout::class);
    }

    // Planos de treino
    public function workoutPlans(): HasMany
    {
        return $this->hasMany(WorkoutPlan::class);
    }

    // Histórico de treinos realizados
    public function workoutLogs(): HasMany
    {
        return $this->hasMany(WorkoutLog::class);
    }
}