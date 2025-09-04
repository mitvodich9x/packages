<?php

namespace Vgplay\Recharge\Models;

use Vgplay\Games\Models\Game;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Payment extends Model
{
    protected $fillable = ['name', 'alias', 'image', 'description', 'promotion', 'active', 'sort'];
    protected $casts = ['promotion' => 'array', 'active' => 'boolean'];
}
