<?php

namespace Vgplay\Games\Models\Wallet;

use Illuminate\Database\Eloquent\Model;

/**
 * Class WalletLog
 *
 * @property int    $wallet_log_id
 * @property string $wallet_log_uid
 * @property string $wallet_log_txid
 * @property int    $wallet_log_amount
 * @property string $wallet_log_source
 * @property string $wallet_log_partner_id
 * @property int    $wallet_log_status
 * @property string $wallet_log_reason
 * @property int    $wallet_log_game_id
 * @property int    $wallet_log_total_cash_in
 * @property int    $wallet_log_bonus
 * @property int    $create_time
 * @property int    $modify_time
 *
 * @package App\Models
 */
class WalletLog extends Model
{
    protected $connection = 'wallet';
    protected $table = 'wallet_log';
    protected $primaryKey = 'wallet_log_id';
    public $timestamps = false;

    protected $casts = [
        'wallet_log_amount'        => 'int',
        'wallet_log_status'        => 'int',
        'wallet_log_game_id'       => 'int',
        'wallet_log_total_cash_in' => 'int',
        'wallet_log_bonus'         => 'int',
        'create_time'              => 'int',
        'modify_time'              => 'int'
    ];

    protected $fillable = [
        'wallet_log_uid',
        'wallet_log_txid',
        'wallet_log_amount',
        'wallet_log_source',
        'wallet_log_partner_id',
        'wallet_log_status',
        'wallet_log_reason',
        'wallet_log_game_id',
        'wallet_log_total_cash_in',
        'wallet_log_bonus',
        'create_time',
        'modify_time'
    ];

    /**
     * @param $index_raw
     * @return static
     */
    public static function IndexRaw($index_raw)
    {
        $model = new static();
        $model->setTable(\DB::raw($model->getTable() . ' ' . $index_raw));
        return $model;
    }
}
