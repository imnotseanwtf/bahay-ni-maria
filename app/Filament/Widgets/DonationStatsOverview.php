<?php

namespace App\Filament\Widgets;

use App\Models\Donation;
use App\Models\FinancialReport;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DonationStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $finanialReportSum = FinancialReport::sum('amount');

        $overallDonations = Donation::count();

        return [
            Stat::make('Overall Donation Amount', '₱' . $finanialReportSum),
            Stat::make('Overall In-Kind Donations', $overallDonations)
        ];
    }
}
