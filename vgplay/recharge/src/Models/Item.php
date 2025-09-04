<?php

namespace Vgplay\Recharge\Models;

use Vgplay\Games\Models\Game;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Item extends Model
{
    protected $fillable = [
        'type',
        'name',
        'code',
        'image',
        'unit',
        'description',
        'vxu_amount',
        'discount_percent',
        'limit_per_user',
        'allow_multiple_per_order',
        'tier',
        'requires_min_tier',
        'is_active',
        'sort',
    ];

    public function games(): BelongsToMany
    {
        return $this->belongsToMany(
            Game::class,
            'game_item',
            'item_id',
            'game_id',
            'id',
            'game_id'
        )->withTimestamps()->withPivot('is_active');
    }

    public function details(): HasMany
    {
        return $this->hasMany(ItemDetail::class);
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(PurchaseHistory::class);
    }
}
