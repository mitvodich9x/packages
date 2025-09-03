<?php

namespace Vgplay\Settings\Providers;

use Inertia\Inertia;
use Vgplay\Settings\Models\Setting;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Vgplay\Settings\Policies\SettingPolicy;
use Vgplay\Settings\Services\SettingService;
use Vgplay\Settings\Observers\SettingObserver;
use Vgplay\Settings\Console\Commands\InstallVgpSettingCommand;

class SettingsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerPolicies();
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallVgpSettingCommand::class,
            ]);
        }

        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->publishes([
            __DIR__ . '/../Database/Seeders' => database_path('Seeders'),
        ], 'vgp-setting-seeders');

        Setting::observe(SettingObserver::class);

        Inertia::share([
            'settings' => function () {
                $service = app(\Vgplay\Settings\Services\SettingService::class);

                return [
                    'footer'   => $service->get('site_content_footer', []),
                    'site'     => $service->getByGroup('site'),
                    'payment'  => $service->getByGroup('payment'),
                ];
            },
        ]);
    }

    protected function registerPolicies(): void
    {
        $policies = [
            Setting::class => SettingPolicy::class,
        ];

        foreach ($policies as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }

    public function register() {}
}
