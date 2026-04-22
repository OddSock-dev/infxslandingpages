<?php

namespace App\Filament\Resources\CrmSyncAttempts\Pages;

use App\Filament\Resources\CrmSyncAttempts\CrmSyncAttemptResource;
use Filament\Resources\Pages\ListRecords;

class ListCrmSyncAttempts extends ListRecords
{
    protected static string $resource = CrmSyncAttemptResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
