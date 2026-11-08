<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SportSkillLevel extends Model
{
    public $timestamps = false;

    protected $table = 'sport_skill_levels';

    protected $fillable = [
        'sport_id',
        'name',
        'level_order',
    ];

    protected function casts(): array
    {
        return [
            'sport_id' => 'integer',
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

    public function userSportProfiles(): HasMany
    {
        return $this->hasMany(UserSportProfile::class, 'skill_level_id');
    }
}

