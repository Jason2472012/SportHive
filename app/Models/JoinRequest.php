<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JoinRequest extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    protected $fillable = [
        'room_id',
        'user_id',
        'message',
        'status',
        'responded_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'responded_at' => 'datetime',
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(OpenRoom::class, 'room_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

