<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    public $timestamps = false;

    protected $table = 'chat_messages';

    protected $fillable = [
        'room_id',
        'sender_id',
        'body',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(OpenRoom::class, 'room_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}

