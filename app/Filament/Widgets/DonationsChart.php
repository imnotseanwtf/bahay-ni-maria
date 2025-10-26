<?php

namespace App\Filament\Widgets;

use App\Models\Donation;
use App\Models\FinancialReport;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Illuminate\Support\Facades\DB;

class DonationsChart extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'donationsChart';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Monthly Donations & Financial Reports';

    /**
     * Widget column span
     *
     * @var string|int|array
     */
    protected int|string|array $columnSpan = 'full';

        /**
     * Specify that this widget should only be used on resource pages
     */
    public static function canView(): bool
    {
        return auth()->user()->isAdmin();
    }

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        // Get current year or you can modify this to get specific year
        $currentYear = now()->year;

        // Get monthly donations count
        $monthlyDonations = Donation::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', $currentYear)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // Get monthly financial reports count
        $monthlyFinancialReports = FinancialReport::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', $currentYear)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // Prepare data for all 12 months
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $donationsData = [];
        $financialReportsData = [];

        for ($i = 1; $i <= 12; $i++) {
            $donationsData[] = $monthlyDonations[$i] ?? 0;
            $financialReportsData[] = $monthlyFinancialReports[$i] ?? 0;
        }

        // Calculate the maximum value to determine Y-axis scaling
        $maxValue = max(array_merge($donationsData, $financialReportsData));
        $yAxisMax = $maxValue > 10 ? null : 10; // Auto-scale if data > 10, otherwise cap at 10

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
                'width' => '100%',
            ],
            'series' => [
                [
                    'name' => 'Cash Donations',
                    'data' => $donationsData,
                ],
                [
                    'name' => 'Financial Reports',
                    'data' => $financialReportsData,
                ],
            ],
            'xaxis' => [
                'categories' => $months,
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'yaxis' => [
                'min' => 0,
                'max' => $yAxisMax,
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'colors' => ['#6ce5e8', '#2a8196'], // Cash donations: #6ce5e8, Financial reports: #2a8196
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 3,
                    'horizontal' => false,
                    'columnWidth' => '85%', // Increased from 70% to use more width
                ],
            ],
            'dataLabels' => [
                'enabled' => false,
            ],
            'legend' => [
                'position' => 'top',
                'horizontalAlign' => 'left',
                'fontFamily' => 'inherit',
            ],
        ];
    }
}
