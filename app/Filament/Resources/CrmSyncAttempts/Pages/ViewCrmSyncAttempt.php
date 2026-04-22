<?php

namespace App\Filament\Resources\CrmSyncAttempts\Pages;

use App\Filament\Resources\CrmSyncAttempts\CrmSyncAttemptResource;
use Filament\Resources\Pages\ViewRecord;

class ViewCrmSyncAttempt extends ViewRecord
{
    protected static string $resource = CrmSyncAttemptResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
