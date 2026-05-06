<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Services\DashboardMetricsService;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AttributionInsightsWidget extends StatsOverviewWidget
{
    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $metrics = app(DashboardMetricsService::class)->attributionOverview();
        $coverage = $metrics['total_submissions'] > 0
            ? ($metrics['attributed_leads'] / $metrics['total_submissions']) * 100
            : 0;

        return [
            Stat::make('Attributed Leads', (string) $metrics['attributed_leads'])
                ->description(sprintf('%.1f%% of submitted consultations include source context', $coverage))
                ->color($metrics['attributed_leads'] > 0 ? 'success' : 'warning'),
            Stat::make('Top Source', $metrics['top_source'])
                ->description("{$metrics['top_source_count']} submitted consultations")
                ->color('info'),
            Stat::make('Top Campaign', $metrics['top_campaign'])
                ->description("{$metrics['top_campaign_count']} attributed leads")
                ->color('primary'),
            Stat::make('Top Entry Page', $metrics['top_entry_page'])
                ->description("{$metrics['top_entry_page_count']} consultations reached this source page")
                ->color('success'),
        ];
    }
}
