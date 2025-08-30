<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class MitadminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $filamentModules = [
            ['path' => 'vgplay/admins', 'namespace' => 'Vgplay\\Admins'],
        ];

        $panel = $panel
            ->default()
            ->id('mitadmin')
            ->path('mitadmin')
            ->authGuard('admin')
            ->brandName('VGPLay Admin Panel')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->sidebarFullyCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages');

        foreach ($filamentModules as $module) {
            $panel
                ->discoverResources(
                    in: base_path($module['path'] . '/src/Filament/Resources'),
                    for: $module['namespace'] . '\\Filament\\Resources'
                )
                ->discoverPages(
                    in: base_path($module['path'] . '/src/Filament/Pages'),
                    for: $module['namespace'] . '\\Filament\\Pages'
                );
        }

        return $panel
            ->pages([
                Dashboard::class,
            ])
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class . ':admin',
            ]);
    }
}
