<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
         FilamentAsset::register([
        Js::make('leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js'),
        Css::make('leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css'),
    ], package: 'app'); // <-- You must specify a package name
    }
}
