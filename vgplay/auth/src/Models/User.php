<?php

namespace Vgplay\Auth\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'username',
        'extras'
    ];

    protected $casts = [
        'extras' => 'array',
    ];

    protected $hidden = [
        'passworrd',
        'remember_token',
    ];
}
