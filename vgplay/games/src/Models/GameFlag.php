<?php

namespace Vgplay\Games\Models;

use Illuminate\Database\Eloquent\Model;

class GameFlag extends Model
{
    protected $fillable = [
        'game_id',
        'flags',
    ];

    protected $casts = [
        'flags' => 'array',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class, 'game_id', 'game_id');
    }
}