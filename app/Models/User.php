<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $authPasswordName = 'password_hash';

    protected $fillable = [
        'name',
        'email',
        'role',
        'password_hash',
        'phone',
        'photo_url',
        'rating_avg',
        'rating_count',
        'is_active',
    ];

    protected $hidden = [
        'password_hash',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'rating_avg' => 'decimal:2',
            'rating_count' => 'integer',
        ];
    }

    public function venues(): HasMany
    {
        return $this->hasMany(Venue::class, 'owner_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'user_id');
    }

    public function roomMemberships(): HasMany
    {
        return $this->hasMany(RoomMember::class, 'user_id');
    }

    public function rooms(): BelongsToMany
    {
        return $this->belongsToMany(OpenRoom::class, 'room_members', 'user_id', 'room_id')
            ->withPivot('joined_at');
    }

    public function joinRequests(): HasMany
    {
        return $this->hasMany(JoinRequest::class, 'user_id');
    }

    public function chatMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'sender_id');
    }

    public function ratingsGiven(): HasMany
    {
        return $this->hasMany(Rating::class, 'rater_id');
    }

    public function ratingsReceived(): HasMany
    {
        return $this->hasMany(Rating::class, 'ratee_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    public function reportsSubmitted(): HasMany
    {
        return $this->hasMany(Report::class, 'reporter_id');
    }

    public function reportsHandled(): HasMany
    {
        return $this->hasMany(Report::class, 'handled_by');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class, 'user_id');
    }

    public function sportProfiles(): HasMany
    {
        return $this->hasMany(UserSportProfile::class, 'user_id');
    }
}
