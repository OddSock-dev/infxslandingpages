<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Services\DashboardMetricsService;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LeadQualitySegmentationWidget extends StatsOverviewWidget
{
    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $metrics = app(DashboardMetricsService::class)->leadQualityOverview();

        return [
            Stat::make('High Intent', (string) $metrics['high_intent'])
                ->description('Urgent timeline and larger team-size signals')
                ->color($metrics['high_intent'] > 0 ? 'success' : 'gray'),
            Stat::make('Warm Pipeline', (string) $metrics['warm_pipeline'])
                ->description('Some urgency or complexity, but not both')
                ->color($metrics['warm_pipeline'] > 0 ? 'warning' : 'gray'),
            Stat::make('Nurture Needed', (string) $metrics['nurture_needed'])
                ->description('Exploratory or lighter-fit consultations')
                ->color($metrics['nurture_needed'] > 0 ? 'info' : 'gray'),
            Stat::make('Best High-Intent Fit', $metrics['top_high_intent_product'])
                ->description("{$metrics['top_high_intent_product_count']} high-intent consultations")
                ->color($metrics['top_high_intent_product_count'] > 0 ? 'success' : 'gray'),
        ];
    }
}
