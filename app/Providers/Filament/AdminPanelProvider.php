<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            
            ->login()
            ->profile()
           
            ->brandName('Deem')
            ->brandLogo(asset('images/logo-deem.png'))
            ->brandLogoHeight('2rem')
            ->favicon(asset('images/logo-deem.png'))
            ->globalSearch(true)
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
         ->colors([
    'primary' => [
        50  => '#eef9ff',
        100 => '#d9f0ff',
        200 => '#b6e3ff',
        300 => '#7fcfff',
        400 => '#38b6ff', // قريب جدًا من لون اللوجو
        500 => '#1ea0ff', // MAIN BRAND COLOR
        600 => '#0b84e6',
        700 => '#0867b4',
        800 => '#0a4f87',
        900 => '#0b3f6b',
        950 => '#062845',
    ],
])

            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                // Widgets disabled for performance
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
            ->plugins([
                FilamentShieldPlugin::make(),
            ])
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
