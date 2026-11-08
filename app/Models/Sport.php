<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sport extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    protected $fillable = [
        'name',
        'is_active',
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
        return $this->hasMany(SportSkillLevel::class, 'sport_id');
    }

    public function courts(): HasMany
    {
        return $this->hasMany(Court::class, 'sport_id');
    }

    public function userProfiles(): HasMany
    {
        return $this->hasMany(UserSportProfile::class, 'sport_id');
    }
}

