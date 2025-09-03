<?php

namespace Vgplay\Games\Models;

use Vgplay\Games\Models\Game;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

class GameAdmin extends Model
{
    protected $fillable = [
        'game_id',
        'name',
        'desc',
        'avatar',
        'facebook_url',
        'zalo_url',
        'telegram',
        'phone',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class, 'game_id', 'game_id');
    }

    
    public function getAvatarUrlAttribute(): ?string
    {
        return $this->avatar ? Storage::disk('ftp')->url($this->avatar) : null;
    }

    public function getZaloUrlUrlAttribute(): ?string
    {
        return $this->zalo_url ? Storage::disk('ftp')->url($this->zalo_url) : null;
    }
    // protected function avatar(): Attribute
    // {
    //     return Attribute::make(
    //         get: fn(string $value = null) => $value ? $value : null,
    //     );
    // }

    // protected function zaloUrl(): Attribute
    // {
    //     return Attribute::make(
    //         get: fn(string $value = null) => $value ? $value : null,
    //     );
    // }
}
