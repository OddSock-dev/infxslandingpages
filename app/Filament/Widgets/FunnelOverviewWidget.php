<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Services\DashboardMetricsService;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FunnelOverviewWidget extends StatsOverviewWidget
{
    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $metrics = app(DashboardMetricsService::class)->funnelOverview();

        return [
            Stat::make('Journeys Started', (string) $metrics['journeys_started'])
                ->description('Homepage qualification journeys in the last 30 days')
                ->chart($metrics['journey_trend'])
                ->color('primary'),
            Stat::make('Recommendations Issued', (string) $metrics['recommendations_issued'])
                ->description(sprintf('%.1f%% of journeys reached a product recommendation', $metrics['routed_rate']))
                ->chart($metrics['recommendation_trend'])
                ->color('success'),
            Stat::make('Journey Consultations', (string) $metrics['journey_consultations'])
                ->description(sprintf('%.1f%% of journeys became a submitted consultation', $metrics['submission_rate']))
                ->chart($metrics['consultation_trend'])
                ->color('info'),
            Stat::make('Free Trial Starts', (string) $metrics['trial_starts'])
                ->description('Recorded unique launches from the product-page trial CTA')
                ->chart($metrics['trial_start_trend'])
                ->color('warning'),
            Stat::make('Stalled Handoffs', (string) $metrics['stalled_handoffs'])
                ->description(sprintf('%.1f%% of recommendations expired without a submission', $metrics['drop_off_rate']))
                ->chart($metrics['stalled_trend'])
                ->color($metrics['stalled_handoffs'] > 0 ? 'warning' : 'success'),
        ];
    }
}
