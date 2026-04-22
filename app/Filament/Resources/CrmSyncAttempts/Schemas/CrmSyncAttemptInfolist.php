<?php

declare(strict_types=1);

namespace App\Filament\Resources\CrmSyncAttempts\Schemas;

use App\Enums\SyncAttemptStatus;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CrmSyncAttemptInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Attempt')
                    ->schema([
                        TextEntry::make('id')->label('ID'),
                        TextEntry::make('submission_id'),
                        TextEntry::make('provider'),
                        TextEntry::make('action'),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (SyncAttemptStatus $state): string => match ($state) {
                                SyncAttemptStatus::Pending => 'warning',
                                SyncAttemptStatus::Success => 'success',
                                SyncAttemptStatus::Failed => 'danger',
                            }),
                        TextEntry::make('attempted_at')
                            ->dateTime(),
                    ])
                    ->columns(2),

                Section::make('Error Details')
                    ->schema([
                        TextEntry::make('error_code'),
                        TextEntry::make('error_message')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }
}
