<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Court extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'venue_id',
        'sport_id',
        'name',
        'is_active',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class, 'venue_id');
    }

    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class, 'sport_id');
    }

    public function timeSlots(): HasMany
    {
        return $this->hasMany(TimeSlot::class, 'court_id');
    }

    public function deactivateField(): void
    {
        $this->update(['is_active' => false]);
    }
}

