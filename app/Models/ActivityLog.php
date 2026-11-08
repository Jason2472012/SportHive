<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    public $timestamps = false;

    protected $table = 'activity_logs';

    protected $fillable = [
        'user_id',
        'action',
        'detail',
        'ip_address',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function recordLog(?int $userId, string $action, ?string $detail = null, ?string $ipAddress = null): self
    {
        return self::query()->create([
            'user_id' => $userId,
            'action' => $action,
            'detail' => $detail,
            'ip_address' => $ipAddress ?? request()?->ip(),
            'created_at' => now(),
        ]);
    }
}

