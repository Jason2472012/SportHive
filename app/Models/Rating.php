<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rating extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'room_id',
        'rater_id',
        'ratee_id',
        'stars',
        'comment',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'stars' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(OpenRoom::class, 'room_id');
    }

    public function rater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rater_id');
    }

    public function ratee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ratee_id');
    }

    public function validateScoreRange(): bool
    {
        return $this->stars >= 1 && $this->stars <= 5;
    }
}

