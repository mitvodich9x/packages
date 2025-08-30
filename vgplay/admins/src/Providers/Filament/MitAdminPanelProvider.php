<?php

namespace Vgplay\Admins\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class MitAdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $filamentModules = [
            ['path' => 'vgplay/admins', 'namespace' => 'Vgplay\\Admins'],
            ['path' => 'vgplay/settings', 'namespace' => 'Vgplay\\Settings'],
            ['path' => 'vgplay/games', 'namespace' => 'Vgplay\\Games'],
            ['path' => 'vgplay/payments', 'namespace' => 'Vgplay\\Payments'],
            ['path' => 'vgplay/items', 'namespace' => 'Vgplay\\Items'],
        ];

        $panel = $panel
            ->default()
            ->id('mitadmin')
            ->path('mitadmin')
            ->login()
            ->authGuard('admin')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->sidebarFullyCollapsibleOnDesktop()
            ->brandName('VGPLAY ADMIN DASHBOARD')
            ->discoverResources(in: __DIR__ . '/../../Filament/Resources', for: 'Vgplay\Admins\\Filament\\Resources')
            ->discoverPages(in: __DIR__ . '/../../Filament/Pages', for: 'Vgplay\Admins\\Filament\\Pages');

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
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: __DIR__ . '/../../Filament/Widgets', for: 'Vgplay\MitAdmin\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
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
