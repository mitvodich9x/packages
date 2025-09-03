<?php

namespace Vgplay\Games\Models;

use Carbon\Carbon;
use MongoDB\Laravel\Eloquent\Model as Eloquent;

/**
 * Class TopDailyRechargeTotal
 *
 * @property int         $game_id
 * @property int         $vgp_id
 * @property string      $vgp_username
 * @property int         $total
 * @property string|null $last_server_id
 * @property Carbon|null $last_recharge
 * @property Carbon|null $last_login
 * @property Carbon|null $created_at
 */
class TopDailyRechargeTotal extends Eloquent
{
    public const SOURCE_TOPUP         = 'topup';
    public const SOURCE_BIRTHDAY      = 'birthday';
    public const SOURCE_SYSTEM        = 'system';
    public const SOURCE_WHEEL         = 'wheel';
    public const SOURCE_USER_PURCHASE = 'user_purchase';
    public const SOURCE_ADMIN         = 'admin';

    protected $connection = 'mongodb_statistic'; // tên connection trong config/database.php
    protected $collection = 'top_daily_recharge_total'; // tên collection trong MongoDB
    public $timestamps = false;
    protected $guarded = [];
    
    protected $casts = [
        'game_id'       => 'int',
        'vgp_id'        => 'int',
        'total'         => 'int',
        'last_recharge' => 'datetime',
        'last_login'    => 'datetime',
        'created_at'    => 'datetime',
        '_id'           => 'string', // nếu muốn ObjectId thành string khi trả API
    ];

    protected $fillable = [
        'game_id',
        'vgp_id',
        'vgp_username',
        'total',
        'last_server_id',
        'last_recharge',
        'last_login',
        'created_at',
    ];
}
