<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Services\DashboardMetricsService;
use Filament\Widgets\ChartWidget;

class FunnelPagePerformanceWidget extends ChartWidget
{
    protected ?string $heading = 'Funnel Conversion by Page';

    protected ?string $pollingInterval = null;

    protected function getData(): array
    {
        $metrics = app(DashboardMetricsService::class)->pageConversion();

        return [
            'datasets' => [
                [
                    'label' => 'Journey starts',
                    'data' => $metrics['journey_starts'],
                    'backgroundColor' => '#f59e0b',
                ],
                [
                    'label' => 'Submitted consultations',
                    'data' => $metrics['consultations'],
                    'backgroundColor' => '#1d4ed8',
                ],
            ],
            'labels' => $metrics['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
