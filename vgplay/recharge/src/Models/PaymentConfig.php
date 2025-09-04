<?php

namespace Vgplay\Recharge\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentConfig extends Model
{
    protected $fillable = ['payment_id', 'price', 'promotion', 'is_active'];

    protected $casts = [
        'price' => 'integer',
        'promotion' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
}
