<?php

declare(strict_types=1);

namespace App\Filament\Resources\CrmSyncAttempts\Tables;

use App\Enums\SyncAttemptStatus;
use App\Jobs\SyncSubmissionToZohoJob;
use App\Models\CrmSyncAttempt;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CrmSyncAttemptsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('submission_id')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (SyncAttemptStatus $state): string => match ($state) {
                        SyncAttemptStatus::Pending => 'warning',
                        SyncAttemptStatus::Success => 'success',
                        SyncAttemptStatus::Failed => 'danger',
                    })
                    ->sortable(),
                TextColumn::make('provider')
                    ->searchable(),
                TextColumn::make('attempted_at')
                    ->dateTime()
                    ->sortable()
                    ->since(),
                TextColumn::make('error_message')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('retry')
                    ->label('Retry Sync')
                    ->icon('heroicon-o-arrow-path')
                    ->requiresConfirmation()
                    ->visible(fn (CrmSyncAttempt $record): bool => $record->status === SyncAttemptStatus::Failed)
                    ->action(function (CrmSyncAttempt $record): void {
                        SyncSubmissionToZohoJob::dispatch($record->submission_id);
                    }),
            ])
            ->defaultSort('attempted_at', 'desc');
    }
}
