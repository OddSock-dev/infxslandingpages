<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\CrmStatus;
use App\Models\Submission;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SubmissionStatsWidget extends StatsOverviewWidget
{
    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Submissions', Submission::query()->count()),
            Stat::make('Pending Sync', Submission::query()->where('crm_status', CrmStatus::Pending)->count()),
            Stat::make('Synced', Submission::query()->where('crm_status', CrmStatus::Synced)->count()),
            Stat::make('Failed', Submission::query()->where('crm_status', CrmStatus::Failed)->count()),
        ];
    }
}
