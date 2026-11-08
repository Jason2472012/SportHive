<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'slot_id',
        'status',
        'total_price',
        'payment_ref',
        'e_ticket_code',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'total_price' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function slot(): BelongsTo
    {
        return $this->belongsTo(TimeSlot::class, 'slot_id');
    }

    public function openRoom(): HasOne
    {
        return $this->hasOne(OpenRoom::class, 'booking_id');
    }

    public function canOpenRoom(): bool
    {
        return $this->status === 'PAID' && ! $this->openRoom()->exists();
    }
}

