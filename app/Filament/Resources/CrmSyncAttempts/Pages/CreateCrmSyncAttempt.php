<?php

namespace App\Filament\Resources\CrmSyncAttempts\Pages;

use App\Filament\Resources\CrmSyncAttempts\CrmSyncAttemptResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCrmSyncAttempt extends CreateRecord
{
    protected static string $resource = CrmSyncAttemptResource::class;
}
