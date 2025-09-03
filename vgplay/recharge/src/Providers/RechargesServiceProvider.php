<?php

namespace Vgplay\Recharges\Providers;

use Inertia\Inertia;
use Vgplay\Recharges\Models\Recharge;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Vgplay\Recharges\Policies\RechargePolicy;
use Vgplay\Recharges\Services\RechargeService;
use Illuminate\Support\ServiceProvider;
use Vgplay\Recharges\Observers\RechargeObserver;
use Vgplay\Recharges\Http\Middleware\ShareRechargesMiddleware;
use Vgplay\Recharges\Http\Middleware\ShareRolesMiddleware;
use Vgplay\Recharges\Console\Commands\InstallVgpRechargeCommand;

class RechargesServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallVgpRechargeCommand::class,
            ]);
        }
        $this->app['router']->pushMiddlewareToGroup('web', ShareRechargesMiddleware::class);
        $this->app['router']->pushMiddlewareToGroup('web', ShareRolesMiddleware::class);
        $this->registerPolicies();
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->loadRoutesFrom(__DIR__ . '/../Http/routes.php');
        $this->publishes([
            __DIR__ . '/../Database/Seeders' => database_path('Seeders'),
        ], 'vgp-game-seeders');

        Recharge::observe(RechargeObserver::class);

        Inertia::share([
            'games' => fn() => app(RechargeService::class)->getAll(true),
        ]);
    }

    protected function registerPolicies(): void
    {
        $policies = [
            Recharge::class => RechargePolicy::class
        ];

        foreach ($policies as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }

    public function register() {}
}
