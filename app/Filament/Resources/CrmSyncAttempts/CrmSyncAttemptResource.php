<?php

namespace App\Filament\Resources\CrmSyncAttempts;

use App\Filament\Resources\CrmSyncAttempts\Pages\ListCrmSyncAttempts;
use App\Filament\Resources\CrmSyncAttempts\Pages\ViewCrmSyncAttempt;
use App\Filament\Resources\CrmSyncAttempts\Schemas\CrmSyncAttemptForm;
use App\Filament\Resources\CrmSyncAttempts\Schemas\CrmSyncAttemptInfolist;
use App\Filament\Resources\CrmSyncAttempts\Tables\CrmSyncAttemptsTable;
use App\Models\CrmSyncAttempt;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CrmSyncAttemptResource extends Resource
{
    protected static ?string $model = CrmSyncAttempt::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return CrmSyncAttemptForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CrmSyncAttemptInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CrmSyncAttemptsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCrmSyncAttempts::route('/'),
            'view' => ViewCrmSyncAttempt::route('/{record}'),
        ];
    }
}
