<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JoinRequest extends Model
{
    public $timestamps = false;

    protected $table = 'join_requests';

    protected $fillable = [
        'room_id',
        'user_id',
        'message',
        'status',
        'created_at',
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

    public function acceptRequest(): void
    {
        $this->update([
            'status' => 'ACCEPTED',
            'responded_at' => now(),
        ]);
    }

    public function rejectRequest(): void
    {
        $this->update([
            'status' => 'REJECTED',
            'responded_at' => now(),
        ]);
    }
}

