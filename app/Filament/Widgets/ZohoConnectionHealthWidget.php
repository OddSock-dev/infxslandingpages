<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Filament\Resources\CrmSyncAttempts\CrmSyncAttemptResource;
use App\Services\DashboardMetricsService;
use App\Services\ZohoConnectionSettings;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ZohoConnectionHealthWidget extends StatsOverviewWidget
{
    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $summary = app(ZohoConnectionSettings::class)->summary();
        $metrics = app(DashboardMetricsService::class)->syncHealth();
        $lastSuccessAt = $metrics['last_success_at'];

        return [
            Stat::make('Zoho Connection', $summary['has_refresh_token'] ? 'Connected' : 'Needs authorisation')
                ->description($summary['connection_message'])
                ->color($summary['has_refresh_token'] ? 'success' : 'warning'),
            Stat::make('Access Token', $summary['token_status'])
                ->description($summary['token_message'])
                ->color(match ($summary['token_status']) {
                    'Valid' => 'success',
                    'Expiring Soon' => 'warning',
                    default => 'danger',
                }),
            Stat::make('Failed Pushes (24h)', (string) $metrics['failed_last_day'])
                ->description('CRM sync attempts that need operator attention')
                ->color($metrics['failed_last_day'] > 0 ? 'danger' : 'success')
                ->url(CrmSyncAttemptResource::getUrl()),
            Stat::make('Pending Sync', (string) $metrics['pending_submissions'])
                ->description('Queued or waiting for the next Zoho sync attempt')
                ->color($metrics['pending_submissions'] > 0 ? 'warning' : 'success'),
            Stat::make('Sync Success (7d)', sprintf('%.1f%%', $metrics['success_rate_last_7d']))
                ->description(
                    $metrics['completed_attempts_last_7d'] > 0
                        ? "{$metrics['successful_attempts_last_7d']} of {$metrics['completed_attempts_last_7d']} completed attempts succeeded"
                        : 'No completed CRM pushes in the last 7 days'
                )
                ->color(match (true) {
                    $metrics['success_rate_last_7d'] >= 95.0 => 'success',
                    $metrics['success_rate_last_7d'] >= 80.0 => 'warning',
                    default => 'danger',
                }),
            Stat::make('Last Successful Push', $lastSuccessAt?->diffForHumans() ?? 'Never')
                ->description($lastSuccessAt?->format('Y-m-d H:i') ?? 'No successful CRM push has been recorded yet')
                ->color(match (true) {
                    $lastSuccessAt === null => 'danger',
                    $lastSuccessAt->lt(now()->subDay()) => 'warning',
                    default => 'success',
                }),
        ];
    }
}
