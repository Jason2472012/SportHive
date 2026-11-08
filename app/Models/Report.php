<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'reporter_id',
        'target_type',
        'target_id',
        'reason',
        'status',
        'handled_by',
        'created_at',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function updateReportStatus(string $status, ?int $handlerId = null): void
    {
        $this->update([
            'status' => $status,
            'handled_by' => $handlerId,
            'resolved_at' => in_array($status, ['RESOLVED', 'DISMISSED'], true) ? now() : null,
        ]);
    }
}

