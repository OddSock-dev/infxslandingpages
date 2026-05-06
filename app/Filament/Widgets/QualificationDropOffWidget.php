<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Services\DashboardMetricsService;
use Filament\Widgets\ChartWidget;

class QualificationDropOffWidget extends ChartWidget
{
    protected ?string $heading = 'Qualification Drop-off by Recommendation';

    protected ?string $pollingInterval = null;

    protected function getData(): array
    {
        $metrics = app(DashboardMetricsService::class)->qualificationDropOff();

        return [
            'datasets' => [
                [
                    'label' => 'Open handoffs',
                    'data' => $metrics['open'],
                    'backgroundColor' => '#38bdf8',
                ],
                [
                    'label' => 'Stalled handoffs',
                    'data' => $metrics['stalled'],
                    'backgroundColor' => '#f97316',
                ],
                [
                    'label' => 'Submitted consultations',
                    'data' => $metrics['submitted'],
                    'backgroundColor' => '#22c55e',
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
