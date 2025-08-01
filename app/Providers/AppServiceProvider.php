<?php

namespace App\Providers;


use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        FilamentView::registerRenderHook(
            PanelsRenderHook::BODY_START,
            fn(): string => filament()->getCurrentPanel()?->getId() === 'manpower'
            ? Blade::render('@livewire(\'custom-navbar\')')
            : ''
        );

        FilamentView::registerRenderHook(
            PanelsRenderHook::BODY_END,
            fn(): string => filament()->getCurrentPanel()?->getId() === 'manpower'
            ? Blade::render('@livewire(\'custom-bottombar\')')
            : ''
        );

        FilamentView::registerRenderHook(
            PanelsRenderHook::SIDEBAR_NAV_START,
            fn(): string => filament()->getCurrentPanel()?->getId() === 'admin'
            ? Blade::render('@livewire(\'custom-sidebar\')') : ''
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
