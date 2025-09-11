<?php

namespace App\Filament\Widgets;

use App\Models\RealtimeLocation;
use Filament\Widgets\Widget;
use Illuminate\Contracts\View\View;

class MapWidget extends Widget
{
    protected static ?string $heading = 'Map Monitoring';

    public ?int $patient_id = null;

    public static ?bool $can_view = false;

    protected static ?string $pollingInterval = null; // Remove automatic polling

    public function poll(): void
    {
        $this->updateLocation();
        
        // Always dispatch the event, even if coordinates are null
        $this->dispatch('locationUpdated', [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'timestamp' => now()->timestamp // Add timestamp to force event uniqueness
        ]);
    }

    protected static bool $isLazy = true;

    protected int | string | array $columnSpan = 'full';

    protected static string $view = 'filament.widgets.map-widget';

    public ?float $latitude = null;
    public ?float $longitude = null;
    public $timestamp = null;

    public static function canView(): bool
    {
        return static::$can_view;
    }

    public function mount(): void
    {
        $this->updateLocation();
    }

    public function updateLocation(): void
    {
        if ($this->patient_id) {
            $location = RealtimeLocation::where('patient_id', $this->patient_id)
                // ->where('created_at', '>=', now()->subMinutes(5))
                ->latest()
                ->first();

            if ($location) {
                $this->latitude = (float) $location->latitude;
                $this->longitude = (float) $location->longitude;
                $this->timestamp = $location->created_at;
            } else {
                $this->latitude = null;
                $this->longitude = null;
            }
        }
    }

    public function render(): View
    {
        // Update location data on each render (polling)
        $this->updateLocation();
        
        return view(static::$view);
    }
}