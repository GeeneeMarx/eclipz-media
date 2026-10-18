<?php

namespace App\Filament\Widgets;

use App\Support\DashboardMetrics;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InquiryStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected static bool $isLazy = false;

    protected function getColumns(): int
    {
        return 4;
    }

    protected function getStats(): array
    {
        $inquiries = DashboardMetrics::inquiryCounts();
        $projects = DashboardMetrics::projectCounts();

        return [
            Stat::make('Total enquiries', $inquiries['total'])
                ->description($inquiries['last_7_days'].' in the last 7 days'),
            Stat::make('New (unreviewed)', $inquiries['new'])
                ->description('Waiting for a first review')
                ->color($inquiries['new'] > 0 ? 'warning' : 'gray'),
            Stat::make('Under review', $inquiries['under_review'])
                ->description('Being looked at')
                ->color('info'),
            Stat::make('Responded', $inquiries['responded'])
                ->description('Marked as responded')
                ->color('primary'),
            Stat::make('Closed', $inquiries['closed'])
                ->description('Closed enquiries')
                ->color('success'),
            Stat::make('Email problems', $inquiries['email_problems'])
                ->description($inquiries['email_problems'] > 0 ? 'Enquiries with a failed email' : 'No failed emails recorded')
                ->color($inquiries['email_problems'] > 0 ? 'danger' : 'gray'),
            Stat::make('Published projects', $projects['published'])
                ->description('Visible on the public site')
                ->color('success'),
            Stat::make('Draft projects', $projects['draft'])
                ->description('Not yet published')
                ->color('gray'),
        ];
    }
}