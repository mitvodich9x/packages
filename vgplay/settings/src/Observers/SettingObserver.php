<?php

namespace Vgplay\Settings\Observers;

use Vgplay\Settings\Models\Setting;
use Vgplay\Settings\Services\SettingService;

class SettingObserver
{
    public function saved(Setting $setting): void
    {
        app(SettingService::class)->syncAll();
    }

    public function deleted(Setting $setting): void
    {
        app(SettingService::class)->syncAll();
    }
}
