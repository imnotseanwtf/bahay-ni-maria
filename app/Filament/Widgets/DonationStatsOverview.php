<?php

namespace App\Filament\Widgets;

use App\Models\Donation;
use App\Models\FinancialReport;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DonationStatsOverview extends BaseWidget
{

    protected ?string $heading = 'Donation Statistics';

            /**
     * Specify that this widget should only be used on resource pages
     */
    public static function canView(): bool
    {
        return auth()->user()->isAdmin();
    }


    protected function getStats(): array
    {
        $financialReportSumRaw = FinancialReport::sum('amount');
        $financialReportSum = number_format($financialReportSumRaw, 2, '.', ',');
        
        $overallDonations = Donation::count();

        return [
            Stat::make('Overall Donation Amount', '₱' . $financialReportSum),
            Stat::make('Overall In-Kind Donations', $overallDonations)
        ];
    }
}
