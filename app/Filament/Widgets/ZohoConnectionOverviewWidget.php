<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Services\ZohoConnectionSettings;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ZohoConnectionOverviewWidget extends StatsOverviewWidget
{
    protected ?string $pollingInterval = null;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $summary = app(ZohoConnectionSettings::class)->summary();

        return [
            Stat::make('Connection', $summary['has_refresh_token'] ? 'Connected' : 'Needs authorisation')
                ->description($summary['connection_message'])
                ->color($summary['has_refresh_token'] ? 'success' : 'warning'),
            Stat::make('Access Token', $summary['token_status'])
                ->description($summary['token_message'])
                ->color(match ($summary['token_status']) {
                    'Valid' => 'success',
                    'Expiring Soon' => 'warning',
                    default => 'danger',
                }),
            Stat::make('Authorization Flow', 'Save → Authorize → Sync')
                ->description('Save the OAuth settings, authorize Zoho once, then refresh only when the token needs operator attention.')
                ->color('info'),
        ];
    }
}
