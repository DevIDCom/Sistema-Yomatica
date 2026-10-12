<?php

namespace App\Providers\Filament;

use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->databaseNotifications()
            ->databaseNotificationsPolling(null)
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Blue,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverResources(in: app_path('Domains/Users/Filament/Resources'), for: 'App\\Domains\\Users\\Filament\\Resources')
            ->discoverResources(in: app_path('Domains/Tickets/UI/Filament/Resources'), for: 'App\\Domains\\Tickets\\UI\\Filament\\Resources')
            ->discoverResources(in: app_path('Domains/Clientes/UI/Filament/Resources'), for: 'App\\Domains\\Clientes\\UI\\Filament\\Resources')
            ->discoverResources(in: app_path('Domains/Cotizaciones/UI/Filament/Resources'), for: 'App\\Domains\\Cotizaciones\\UI\\Filament\\Resources')
            ->discoverResources(in: app_path('Domains/Sucursales/UI/Filament/Resources'), for: 'App\\Domains\\Sucursales\\UI\\Filament\\Resources')
            ->discoverResources(in: app_path('Domains/Proyectos/UI/Filament/Resources'), for: 'App\\Domains\\Proyectos\\UI\\Filament\\Resources')
            ->discoverResources(in: app_path('Domains/Productos/UI/Filament/Resources'), for: 'App\\Domains\\Productos\\UI\\Filament\\Resources')

            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
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
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Soporte y Tickets')
                    ->icon('heroicon-o-lifebuoy')
                    ->collapsed(false),

                NavigationGroup::make()
                    ->label('Operaciones y Proyectos')
                    ->icon('heroicon-o-wrench-screwdriver')
                    ->collapsed(false),

                NavigationGroup::make()
                    ->label('Comercial y Ventas')
                    ->icon('heroicon-o-banknotes')
                    ->collapsed(false),

                NavigationGroup::make()
                    ->label('Clientes y CRM')
                    ->icon('heroicon-o-building-office-2')
                    ->collapsed(false),

                NavigationGroup::make()
                    ->label('Administración')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsed(true),
            ]);
    }
}
