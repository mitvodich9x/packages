<?php

namespace Vgplay\Recharge\Providers;

use Inertia\Inertia;
use Vgplay\Games\Models\Game;
use Vgplay\Recharge\Models\Item;
use Illuminate\Support\Facades\Gate;
use Vgplay\Recharge\Models\Recharge;
use Illuminate\Support\Facades\Route;
use Vgplay\Recharge\Models\ItemDetail;
use Illuminate\Support\ServiceProvider;
use Vgplay\Games\Observers\GameObserver;
use Vgplay\Recharge\Models\PaymentMethod;
use Vgplay\Recharge\Models\PurchaseHistory;
use Vgplay\Recharge\Observers\ItemObserver;
use Vgplay\Recharge\Policies\RechargePolicy;
use Vgplay\Recharge\Services\Rechargeervice;
use Vgplay\Recharge\Models\GamePaymentMethod;
use Vgplay\Recharge\Observers\RechargeObserver;
use Vgplay\Recharge\Observers\ItemDetailObserver;
use Vgplay\Recharge\Observers\PaymentMethodObserver;
use Vgplay\Recharge\Observers\PurchaseHistoryObserver;
use Vgplay\Recharge\Observers\GamePaymentMethodObserver;
use Vgplay\Recharge\Http\Middleware\ShareRolesMiddleware;
use Vgplay\Recharge\Http\Middleware\ShareRechargeMiddleware;
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
        ], 'vgp-recharge-seeders');

        Game::observe(GameObserver::class);
        Item::observe(ItemObserver::class);
        ItemDetail::observe(ItemDetailObserver::class);
        GamePaymentMethod::observe(GamePaymentMethodObserver::class);
        PaymentMethod::observe(PaymentMethodObserver::class);
        PurchaseHistory::observe(PurchaseHistoryObserver::class);
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
