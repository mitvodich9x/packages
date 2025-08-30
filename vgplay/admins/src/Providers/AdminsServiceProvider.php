<?php

namespace Vgplay\Admins\Providers;

use Vgplay\Admins\Models\Role;
use Vgplay\Admins\Models\Admin;
use Illuminate\Support\Facades\Gate;
use Vgplay\Admins\Models\Permission;
use Vgplay\Admins\Policies\RolePolicy;
use Illuminate\Support\ServiceProvider;
use Vgplay\Admins\Policies\AdminPolicy;
use Vgplay\Admins\Policies\PermissionPolicy;
use Vgplay\Admins\Console\Commands\InstallMitAdminCommand;
// use Vgplay\Admins\Providers\Filament\MitAdminPanelProvider;

class AdminsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerPolicies();
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallMitAdminCommand::class,
            ]);
        }
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->publishes([
            // __DIR__ . '/../Config/admin.php' => config_path('admin.php'),
            // __DIR__ . '/../Config/filament.php' => config_path('filament.php'),
            __DIR__ . '/../Config/permission.php' => config_path('permission.php'),
        ], 'admins-config');
        $this->publishes([
            __DIR__ . '/../Database/Seeders' => database_path('Seeders'),
        ], 'mit-admin-seeders');

        // $this->mergeAuthConfig();
    }

    protected function registerPolicies(): void
    {
        $policies = [
            Admin::class => AdminPolicy::class,
            Permission::class => PermissionPolicy::class,
            Role::class => RolePolicy::class,
        ];

        foreach ($policies as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }

    public function register()
    {
        // $this->app->register(MitAdminPanelProvider::class);

        // $this->mergeConfigFrom(
        //     __DIR__ . '/../Config/admin.php',
        //     'admin'
        // );
        // $this->mergeConfigFrom(
        //     __DIR__ . '/../Config/filament.php',
        //     'filament'
        // );
        // $this->mergeConfigFrom(
        //     __DIR__ . '/../Config/permission.php',
        //     'permission'
        // );
    }

    // protected function mergeAuthConfig()
    // {
    //     $authConfig = config('auth', []);
    //     $adminAuthConfig = config('admin.auth', []);

    //     // Gộp guards
    //     $authConfig['guards'] = array_merge(
    //         $authConfig['guards'] ?? [],
    //         $adminAuthConfig['guards'] ?? []
    //     );

    //     // Gộp providers
    //     $authConfig['providers'] = array_merge(
    //         $authConfig['providers'] ?? [],
    //         $mitAdminAuthConfig['providers'] ?? []
    //     );

    //     // Cập nhật cấu hình auth
    //     config(['auth' => $authConfig]);
    // }
}
