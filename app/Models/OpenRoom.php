<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OpenRoom extends Model
{
    protected $table = 'open_rooms';

    protected $fillable = [
        'booking_id',
        'title',
        'description',
        'type',
        'quota',
        'fee_per_person',
        'skill_target_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'quota' => 'integer',
            'fee_per_person' => 'decimal:2',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function skillTarget(): BelongsTo
    {
        return $this->belongsTo(SportSkillLevel::class, 'skill_target_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(RoomMember::class, 'room_id');
    }

    public function joinRequests(): HasMany
    {
        return $this->hasMany(JoinRequest::class, 'room_id');
    }

    public function chatMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'room_id');
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class, 'room_id');
    }

    public function getRemainingSlots(): int
    {
        return max(0, (int) $this->quota - $this->members()->count());
    }

    public function isFull(): bool
    {
        return $this->getRemainingSlots() <= 0;
    }
}

