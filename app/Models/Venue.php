<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venue extends Model
{
    protected $fillable = [
        'owner_id',
        'name',
        'address',
        'city',
        'latitude',
        'longitude',
        'description',
        'photo_url',
        'validation_status',
        'validated_by',
        'validated_at',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'validated_at' => 'datetime',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function courts(): HasMany
    {
        return $this->hasMany(Court::class, 'venue_id');
    }

    public function verifyVenue(string $status, ?int $validatorId = null): void
    {
        $this->update([
            'validation_status' => $status,
            'validated_by' => $validatorId,
            'validated_at' => now(),
        ]);
    }
}

