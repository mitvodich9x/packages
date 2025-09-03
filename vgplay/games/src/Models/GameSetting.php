<?php

namespace Vgplay\Games\Models;

use Illuminate\Database\Eloquent\Model;

class GameSetting extends Model
{
    protected $fillable = [
        'game_id',
        'required_vxu',
        'description',
        'content',
        'homepage_url',
        'appstore_url',
        'google_play_url',
        'apk_url',
        'support_url',
        'cdn_url',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }
}
