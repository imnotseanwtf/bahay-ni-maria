<?php

namespace App\Providers\Filament;

use App\Filament\Resources\DiseaseResource;
use App\Filament\Resources\DonationResource;
use App\Filament\Resources\FinancialReportResource;
use App\Filament\Resources\PatientResource;
use App\Filament\Resources\RecentAlertResource;
use App\Filament\Resources\UserResource;
use App\Filament\Widgets\BpmMonitoring;
use App\Filament\Widgets\DonationsChart;
use App\Filament\Widgets\DonationStatsOverview;
use App\Filament\Widgets\MapWidget;
use App\Filament\Widgets\PatientInformationChart;
use App\Filament\Widgets\RecentAlertsTable;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;

class AuthPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('auth')
            ->path('auth')
            ->login()
            ->colors([
                'primary' => Color::Blue,
            ])
            ->resources([
                RecentAlertResource::class,
                PatientResource::class,
                DiseaseResource::class,
                UserResource::class,
                DonationResource::class,
                FinancialReportResource::class,
            ])
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            // ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                    //                Widgets\AccountWidget::class,
//                Widgets\FilamentInfoWidget::class,
                PatientInformationChart::class,
                // RecentAlertsTable::class,
                DonationStatsOverview::class,
                DonationsChart::class,
                BpmMonitoring::class,
                MapWidget::class,
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
                Authenticate::class,
            ])
            ->plugins([
                FilamentApexChartsPlugin::make()
            ])
            ->databaseNotifications() // Enable database notifications
            ->databaseNotificationsPolling('5s')
            ->renderHook(
                PanelsRenderHook::BODY_START,
                fn() => Blade::render('<livewire:notification-monitor />')
            );
    }
}
