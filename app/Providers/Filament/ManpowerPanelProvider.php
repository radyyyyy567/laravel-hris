<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\LoginManpower;
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

class ManpowerPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('manpower')
            ->path('manpower')
            ->colors([
                'primary' => Color::Amber,
            ])->topbar(false)
            ->login(LoginManpower::class) 
            ->discoverResources(in: app_path('Filament/Manpower/Resources'), for: 'App\\Filament\\Manpower\\Resources')
            ->discoverPages(in: app_path('Filament/Manpower/Pages'), for: 'App\\Filament\\Manpower\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->darkMode(false)
            ->discoverWidgets(in: app_path('Filament/Manpower/Widgets'), for: 'App\\Filament\\Manpower\\Widgets')
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
            ->navigation(false)
            ->authMiddleware([
                Authenticate::class,
            ])->viteTheme('resources/css/filament/admin/theme.css')->plugin(\TomatoPHP\FilamentPWA\FilamentPWAPlugin::make());
    }
}
