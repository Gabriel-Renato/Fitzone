<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonalClient extends Model
{
    protected $fillable = [
        'personal_id',
        'client_id',
        'status',
        'start_date',
        'end_date',
        'goals',
        'observations',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function personal(): BelongsTo
    {
        return $this->belongsTo(User::class, 'personal_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }
}