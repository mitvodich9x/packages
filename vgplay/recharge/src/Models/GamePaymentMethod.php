<?php

namespace Vgplay\Recharge\Models;

use Vgplay\Recharge\Models\PaymentMethod;
use Vgplay\Games\Models\Game;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GamePaymentMethod extends Model
{
    protected $table = 'game_payment_method';

    protected $fillable = [
        'game_id',
        'payment_method_id',
        'exchange_rate',
        'min_amount',
        'max_amount',
        'promoption',
        'status'
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class, 'game_id', 'game_id');
    }

    public function method(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }
}
