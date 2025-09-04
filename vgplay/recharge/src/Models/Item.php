<?php

namespace Vgplay\Recharge\Models;

use Illuminate\Support\Str;
use Vgplay\Games\Models\Game;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Item extends Model
{
    protected $fillable = [
        'name',
        'code',
        'image',
        'description',
        'type',
        'unit',
        'amount',
        'discount',
        'details',
        'is_global',
        'allow_multiple',
        'limit_per_user',
        'unlock_min_buys',
        'unlock_price_ceiling',
        'active',
        'sort'
    ];
    
    protected $casts = [
        'details' => 'array',
        'is_global' => 'boolean',
        'allow_multiple' => 'boolean',
        'active' => 'boolean'
    ];

    public function games()
    {
        return $this->belongsToMany(Game::class, 'game_id', 'game_id');
    }
}
