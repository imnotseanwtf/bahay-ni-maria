<?php

namespace App\Filament\Widgets;

use App\Models\SensorsValue;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class BpmMonitoring extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'bpmMonitoring';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'BPM Monitoring';

    /**
     * Define the property to store the patient_id
     */
    public ?int $patient_id = null;

    /**
     * Define the property to store the visibility flag
     */
    public static ?bool $can_view = false;

    /**
     * Set polling interval to auto-refresh data (in seconds)
     */
    protected static ?string $pollingInterval = '1s';

    /**
     * Prevent this widget from showing on the dashboard
     */
    protected static bool $isLazy = true;

    /**
     * Make the widget full width
     */
    protected int | string | array $columnSpan = 'full';

    /**
     * Specify that this widget should only be used on resource pages
     */
    public static function canView(): bool
    {
        return static::$can_view;
    }

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        // Get BPM readings - Fixed the column name issue
        $bpmReadings = SensorsValue::where('patient_id', $this->patient_id)
            ->whereNotNull('bpm')
            ->select('bpm', 'created_at')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->reverse();

        // Extract BPM values - This was the main issue: changed from 'bpm_value' to 'bpm'
        $bpmValues = $bpmReadings->pluck('bpm')->map(function ($value) {
            return is_numeric($value) ? (int) $value : 0;
        })->toArray();

        // Format dates for better readability
        $dates = $bpmReadings->pluck('created_at')->map(function ($date) {
            return $date->format('M j, g:i A'); // e.g., "Jul 5, 2:30 PM"
        })->toArray();

        return [
            'chart' => [
                'type' => 'line',
                'height' => 300,
                'width' => '100%',
                'toolbar' => [
                    'show' => false,
                ],
            ],
            'series' => [
                [
                    'name' => 'Heart Rate (BPM)',
                    'data' => $bpmValues,
                ],
            ],
            'xaxis' => [
                'categories' => $dates,
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'yaxis' => [
                'min' => 40,
                'max' => 200,
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
                'title' => [
                    'text' => 'BPM',
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'colors' => ['#dc2626'],
            'stroke' => [
                'curve' => 'straight',
                'width' => 3,
            ],
            'markers' => [
                'size' => 5,
                'colors' => ['#dc2626'],
                'strokeColors' => '#ffffff',
                'strokeWidth' => 2,
            ],
            'dataLabels' => [
                'enabled' => false,
            ],
            'tooltip' => [
                'enabled' => true,
                'x' => [
                    'format' => 'dd MMM yyyy, hh:mm tt',
                ],
                'y' => [
                    'formatter' => "function(value) { return value + ' BPM'; }",
                ],
            ],
            'grid' => [
                'show' => true,
                'borderColor' => '#e5e7eb',
            ],
            'annotations' => [
                'yaxis' => [
                    [
                        'y' => 60,
                        'y2' => 100,
                        'fillColor' => '#10b981',
                        'opacity' => 0.1,
                        'label' => [
                            'text' => 'Normal Range (60-100 BPM)',
                            'style' => [
                                'color' => '#10b981',
                                'background' => '#ffffff',
                            ],
                        ],
                    ],
                    [
                        'y' => 40,
                        'y2' => 60,
                        'fillColor' => '#f59e0b',
                        'opacity' => 0.1,
                        'label' => [
                            'text' => 'Low',
                            'style' => [
                                'color' => '#f59e0b',
                                'background' => '#ffffff',
                            ],
                        ],
                    ],
                    [
                        'y' => 100,
                        'y2' => 200,
                        'fillColor' => '#ef4444',
                        'opacity' => 0.1,
                        'label' => [
                            'text' => 'High',
                            'style' => [
                                'color' => '#ef4444',
                                'background' => '#ffffff',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}