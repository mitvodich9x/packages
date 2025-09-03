<?php

namespace Vgplay\Games\Providers;

use Inertia\Inertia;
use Vgplay\Games\Models\Game;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Vgplay\Games\Policies\GamePolicy;
use Vgplay\Games\Services\GameService;
use Illuminate\Support\ServiceProvider;
use Vgplay\Games\Observers\GameObserver;
use Vgplay\Games\Http\Middleware\ShareGamesMiddleware;
use Vgplay\Games\Http\Middleware\ShareRolesMiddleware;
use Vgplay\Games\Console\Commands\InstallVgpGameCommand;

class GamesServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallVgpGameCommand::class,
            ]);
        }
        $this->app['router']->pushMiddlewareToGroup('web', ShareGamesMiddleware::class);
        $this->app['router']->pushMiddlewareToGroup('web', ShareRolesMiddleware::class);
        $this->registerPolicies();
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->loadRoutesFrom(__DIR__ . '/../Http/routes.php');
        $this->publishes([
            __DIR__ . '/../Database/Seeders' => database_path('Seeders'),
        ], 'vgp-game-seeders');

        Game::observe(GameObserver::class);

        Inertia::share([
            'games' => fn() => app(GameService::class)->getAll(true),
        ]);
    }

    protected function registerPolicies(): void
    {
        $policies = [
            Game::class => GamePolicy::class
        ];

        foreach ($policies as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }

    public function register() {}
}
