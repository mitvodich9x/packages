<?php

namespace Vgplay\Recharge\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentMethod extends Model
{
    protected $fillable = [
        'alias',
        'name',
        'image',
        'description',
        'promotion_rate',
        'is_active',
        'sort'
    ];

    public function gameConfigs(): HasMany
    {
        return $this->hasMany(GamePaymentMethod::class);
    }
}
