<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SportSkillLevel extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'sport_id',
        'name',
        'level_order',
    ];

    protected function casts(): array
    {
        return [
            'level_order' => 'integer',
        ];
    }

    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class, 'sport_id');
    }

    public function openRooms(): HasMany
    {
        return $this->hasMany(OpenRoom::class, 'skill_target_id');
    }

    public function userProfiles(): HasMany
    {
        return $this->hasMany(UserSportProfile::class, 'skill_level_id');
    }
}

