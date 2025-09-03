<?php

namespace Vgplay\Games\Models;

use Illuminate\Database\Eloquent\Model;

class GameApi extends Model
{
    protected $fillable = [
        'game_id',
        'api_config'
    ];

    protected $casts = [
        'api_config' => 'array',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }
}
