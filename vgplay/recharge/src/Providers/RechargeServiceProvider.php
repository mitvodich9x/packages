<?php

namespace Vgplay\Recharge\Providers;

use Inertia\Inertia;
use Vgplay\Recharge\Models\Recharge;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Vgplay\Recharge\Policies\RechargePolicy;
use Vgplay\Recharge\Services\Rechargeervice;
use Illuminate\Support\ServiceProvider;
use Vgplay\Recharge\Observers\RechargeObserver;
use Vgplay\Recharge\Http\Middleware\ShareRechargeMiddleware;
use Vgplay\Recharge\Http\Middleware\ShareRolesMiddleware;
use Vgplay\Recharge\Console\Commands\InstallVgpRechargeCommand;

class RechargeServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallVgpRechargeCommand::class,
            ]);
        }

        $this->registerPolicies();
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->loadRoutesFrom(__DIR__ . '/../Http/routes.php');
        $this->publishes([
            __DIR__ . '/../Database/Seeders' => database_path('Seeders'),
        ], 'vgp-game-seeders');

        // Recharge::observe(RechargeObserver::class);

        // Inertia::share([
        //     'games' => fn() => app(Rechargeervice::class)->getAll(true),
        // ]);
    }

    protected function registerPolicies(): void
    {
        $policies = [
            // Recharge::class => RechargePolicy::class
        ];

        // foreach ($policies as $model => $policy) {
        //     Gate::policy($model, $policy);
        // }
    }

    public function register() {}
}
