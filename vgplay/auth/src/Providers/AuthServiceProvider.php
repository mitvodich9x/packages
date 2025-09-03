<?php

namespace Vgplay\Auth\Providers;

use Vgplay\Auth\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Vgplay\Auth\Policies\UserPolicy;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Vgplay\Auth\Http\Middleware\ShareAuthMiddleware;
use Vgplay\Auth\Console\Commands\InstallVgpAuthCommand;

class AuthServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallVgpAuthCommand::class,
            ]);
        }

        $this->registerPolicies();
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->loadRoutesFrom(__DIR__ . '/../Http/routes.php');

        Inertia::share('auth', function () {
            $user = Auth::user();

            return [
                'user'        => $user ? $user->only(['id', 'username', 'name', 'avatar_url']) : null,
                'login_type'  => session('login_type'),
                'fb_game_id'  => session('fb_game_id'),
                'fb_game_alias' => session('fb_game_alias'),
            ];
        });
    }

    protected function registerPolicies(): void
    {
        $policies = [
            User::class => UserPolicy::class
        ];

        foreach ($policies as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }

    public function register() {}
}
