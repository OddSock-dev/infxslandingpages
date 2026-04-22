<?php

namespace App\Filament\Resources\CrmSyncAttempts\Pages;

use App\Filament\Resources\CrmSyncAttempts\CrmSyncAttemptResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCrmSyncAttempt extends EditRecord
{
    protected static string $resource = CrmSyncAttemptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
