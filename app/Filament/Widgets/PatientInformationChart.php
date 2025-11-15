<?php

namespace App\Filament\Widgets;

use App\Models\Disease;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PatientInformationChart extends BaseWidget
{
    /**
     * Widget Title
     *
     * @var string|null
     */
    protected ?string $heading = 'Patient Health';

    /**
     * Specify that this widget should only be used on resource pages
     */
    public static function canView(): bool
    {
        return auth()->user()->isAdmin();
    }

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        // Get diseases with patient count
        $diseases = Disease::withCount('patient')->get();
        
        $stats = [];
        
        $colors = ['warning', 'success', 'primary', 'danger', 'info', 'gray'];
        $icons = ['heroicon-o-user-group', 'heroicon-o-heart', 'heroicon-o-shield-check', 'heroicon-o-beaker', 'heroicon-o-clipboard-document-list'];
        
        foreach ($diseases as $index => $disease) {
            $color = $colors[$index % count($colors)];
            $icon = $icons[$index % count($icons)];
            
            $stats[] = Stat::make($disease->name, $disease->patient_count)
                ->description('Total patients')
                ->descriptionIcon($icon)
                ->color($color)
                ->url('/auth/diseases/' . $disease->id . '/patients')
                ->extraAttributes([
                    'class' => 'cursor-pointer hover:scale-105 transition-transform duration-200',
                ]);
        }
        
        return $stats;
    }
}