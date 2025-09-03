<?php

namespace Vgplay\Settings\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'display_name',
        'data_type',
        'value',
        'order',
        'group',
        'type',
        'is_hidden'
    ];

    // protected static function booted(): void
    // {
    //     static::saved(function ($setting) {
    //         app(\Vgplay\Settings\Services\SettingService::class)->set($setting->key, $setting->value);
    //     });

    //     static::deleted(function ($setting) {
    //         app(\Vgplay\Settings\Services\SettingService::class)->forget($setting->key);
    //     });
    // }

    protected $casts = [
        'is_hidden' => 'boolean',
    ];

    protected function value(): Attribute
    {
        return Attribute::make(
            get: fn($value) => (string) $value,
            set: fn($value) =>
            is_string($value) ? $value : json_encode($value, JSON_UNESCAPED_UNICODE)
        );
    }
}
