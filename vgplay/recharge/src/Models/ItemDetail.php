<?php

namespace Vgplay\Recharge\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemDetail extends Model
{
    protected $fillable = ['item_id', 'name', 'image', 'description', 'quantity', 'sort'];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
