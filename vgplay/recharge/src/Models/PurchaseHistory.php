<?php

namespace Vgplay\Recharge\Models;

use Vgplay\Games\Models\Game;
use Vgplay\Recharge\Models\Item;
use Illuminate\Database\Eloquent\Model;
use Vgplay\Recharge\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseHistory extends Model
{
    protected $fillable = [
        'vgp_id',
        'game_id',
        'item_id',
        'payment_method_id',
        'quantity',
        'vxu_amount',
        'price_vnd',
        'status',
        'external_trx_id'
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class, 'game_id', 'game_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function method(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }
}
