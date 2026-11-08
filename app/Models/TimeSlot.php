<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TimeSlot extends Model
{
    protected $table = 'time_slots';

    protected $fillable = [
        'court_id',
        'start_at',
        'end_at',
        'price',
        'status',
        'held_until',
    ];

    protected function casts(): array
    {
        return [
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'price' => 'decimal:2',
            'held_until' => 'datetime',
        ];
    }

    public function court(): BelongsTo
    {
        return $this->belongsTo(Court::class, 'court_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'slot_id');
    }

    public function isAvailable(): bool
    {
        return $this->status === 'AVAILABLE';
    }

    public function getDurationInHours(): float
    {
        if (! $this->start_at || ! $this->end_at) {
            return 0.0;
        }

        return round($this->start_at->diffInMinutes($this->end_at) / 60, 2);
    }
}

