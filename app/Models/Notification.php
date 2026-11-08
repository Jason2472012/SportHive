<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'body',
        'ref_type',
        'ref_id',
        'read_at',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function sendNotification(
        int $userId,
        string $type,
        string $title,
        string $body,
        ?string $refType = null,
        ?int $refId = null
    ): self {
        return self::query()->create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'ref_type' => $refType,
            'ref_id' => $refId,
            'created_at' => now(),
        ]);
    }

    public function markAsRead(): void
    {
        $this->update(['read_at' => now()]);
    }
}

