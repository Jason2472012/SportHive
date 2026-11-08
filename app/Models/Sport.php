<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sport extends Model
{
    public $timestamps = false;

    protected $fillable = [
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

    public function skillLevels(): HasMany
    {
        return $this->hasMany(SportSkillLevel::class, 'sport_id')->orderBy('level_order');
    }

    public function courts(): HasMany
    {
        return $this->hasMany(Court::class, 'sport_id');
    }

    public function userSportProfiles(): HasMany
    {
        return $this->hasMany(UserSportProfile::class, 'sport_id');
    }

    public function setActiveStatus(bool $active): void
    {
        $this->update(['is_active' => $active]);
    }
}

