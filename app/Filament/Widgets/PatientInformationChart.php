<?php

namespace App\Filament\Widgets;

use App\Models\Disease;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Filament\Support\RawJs;

class PatientInformationChart extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'patientInformationChart';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Patient Diseases';

            /**
     * Specify that this widget should only be used on resource pages
     */
    public static function canView(): bool
    {
        return auth()->user()->isAdmin();
    }


    protected int|string|array $columnSpan = 'full';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        // Get diseases with patient count
        $diseases = Disease::withCount('patient')->get();
        
        $labels = $diseases->pluck('name')->toArray();
        $data = $diseases->pluck('patient_count')->toArray();

        return [
            'chart' => [
                'type' => 'pie',
                'height' => 300,
            ],
            'series' => $data,
            'labels' => $labels,
            'colors' => ['#f59e0b', '#10b981', '#3b82f6', '#8b5cf6', '#ef4444', '#f97316', '#06b6d4', '#84cc16'],
            'legend' => [
                'labels' => [
                    'fontFamily' => 'inherit',
                ],
            ],
            'plotOptions' => [
                'pie' => [
                    'donut' => [
                        'labels' => [
                            'show' => true,
                            'name' => [
                                'fontFamily' => 'inherit',
                            ],
                            'value' => [
                                'fontFamily' => 'inherit',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Extra JavaScript options for handling click events
     */
    protected function extraJsOptions(): ?RawJs
    {
        // Get diseases with their IDs for navigation
        $diseases = Disease::withCount('patient')->get();
        $diseaseData = $diseases->map(function ($disease) {
            return [
                'id' => $disease->id,
                'name' => $disease->name,
            ];
        })->toArray();

        return RawJs::make(<<<JS
        {
            chart: {
                events: {
                    dataPointSelection: function(event, chartContext, config) {
                        // Get the clicked disease data
                        const diseaseData = {$this->getDiseaseDataJson()};
                        const clickedIndex = config.dataPointIndex;
                        const clickedDisease = diseaseData[clickedIndex];
                        
                        if (clickedDisease) {
                            // Navigate to the disease patients page
                            const url = '/auth/diseases/' + clickedDisease.id + '/patients';
                            window.location.href = url;
                        }
                    }
                }
            }
        }
        JS);
    }

    /**
     * Get disease data as JSON for JavaScript
     */
    private function getDiseaseDataJson(): string
    {
        $diseases = Disease::withCount('patient')->get();
        $diseaseData = $diseases->map(function ($disease) {
            return [
                'id' => $disease->id,
                'name' => $disease->name,
            ];
        })->toArray();

        return json_encode($diseaseData);
    }
}
