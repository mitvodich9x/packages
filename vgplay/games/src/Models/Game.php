<?php

namespace Vgplay\Games\Models;

use Vgplay\Games\Models\GameApi;
use Vgplay\Recharge\Models\Item;
use Vgplay\Games\Models\GameFlag;
use Vgplay\Games\Models\GameAdmin;
use Vgplay\Games\Models\GameSocial;
use Vgplay\Recharge\Models\Payment;
use Vgplay\Games\Models\GameSetting;
use Vgplay\Recharge\Models\Purchase;
use Vgplay\Games\Traits\HasFtpAssets;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Vgplay\Recharge\Models\GamePaymentMethod;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Game extends Model
{
    use HasFtpAssets;

    protected $primaryKey = 'game_id';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'game_id',
        'name',
        'alias',
        'banner',
        'favicon',
        'icon',
        'logo',
        'thumb',
        'bg_detail',
        'status'
    ];

    protected $with = [
        'admins',
        'apis',
        'socials',
        'flags',
        'settings',
        // 'items'
    ];


    public function admins()
    {
        return $this->hasOne(GameAdmin::class, 'game_id', 'game_id');
    }

    public function apis()
    {
        return $this->hasOne(GameApi::class, 'game_id', 'game_id');
    }

    public function socials()
    {
        return $this->hasOne(GameSocial::class, 'game_id', 'game_id');
    }

    public function flags()
    {
        return $this->hasOne(GameFlag::class, 'game_id', 'game_id');
    }

    public function settings()
    {
        return $this->hasOne(GameSetting::class, 'game_id', 'game_id');
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'game_item', 'game_id', 'item_id')->withTimestamps()->withPivot('is_active');
    }

    public function paymentConfigs(): HasMany
    {
        return $this->hasMany(GamePaymentMethod::class, 'game_id', 'game_id');
    }

    public function getBannerUrlAttribute(): ?string
    {
        return $this->banner ? Storage::disk('ftp')->url($this->banner) : null;
    }

    public function getFaviconUrlAttribute(): ?string
    {
        return $this->favicon ? Storage::disk('ftp')->url($this->favicon) : null;
    }

    public function getIconUrlAttribute(): ?string
    {
        return $this->icon ? Storage::disk('ftp')->url($this->icon) : null;
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo ? Storage::disk('ftp')->url($this->logo) : null;
    }

    public function getThumbUrlAttribute(): ?string
    {
        return $this->thumb ? Storage::disk('ftp')->url($this->thumb) : null;
    }

    public function getBgDetailUrlAttribute(): ?string
    {
        return $this->bg_detail ? Storage::disk('ftp')->url($this->bg_detail) : null;
    }
}
