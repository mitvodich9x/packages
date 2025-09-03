<?php

namespace Vgplay\Games\Models;

use Illuminate\Database\Eloquent\Model;

class GameSocial extends Model
{
    protected $fillable = [
        'app_id',
        'app_secret',
        'fanpage_url',
        'group_url',
        'messenger_url',
        'zalo_oa'
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }
}
