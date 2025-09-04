<?php

namespace Vgplay\Recharge\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Purchase extends Model
{
    protected $fillable = [
        'vgp_id',
        'game_id',
        'item_id',
        'payment_method_id',
        'unit_amount',
        'vnd_paid',
        'status',
        'external_txn_id',
        'meta'
    ];
    protected $casts = ['meta' => 'array'];

    public function scopeSuccess(Builder $q)
    {
        return $q->where('status', 'success');
    }
}
