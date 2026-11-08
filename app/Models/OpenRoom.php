<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OpenRoom extends Model
{
    use HasFactory;

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
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
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

    public function roomMembers(): HasMany
    {
        return $this->hasMany(RoomMember::class, 'room_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'room_members', 'room_id', 'user_id')
            ->withPivot('joined_at');
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
}

