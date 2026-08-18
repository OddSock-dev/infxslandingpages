<?php

declare(strict_types=1);

namespace App\Filament\Resources\Submissions\Tables;

use App\Enums\CrmStatus;
use App\Enums\SyncAttemptStatus;
use App\Filament\Resources\CrmSyncAttempts\CrmSyncAttemptResource;
use App\Models\Submission;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SubmissionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with('latestSyncAttempt:id,submission_id,status'))
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('contact_email')
                    ->label('Email')
                    ->state(function (Submission $record): ?string {
                        $email = data_get($record->pii_json, 'email');

                        return is_string($email) ? $email : null;
                    })
                    ->searchable(false),
                TextColumn::make('product_key')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('crm_status')
                    ->badge()
                    ->color(fn (CrmStatus $state): string => match ($state) {
                        CrmStatus::Pending => 'warning',
                        CrmStatus::Synced => 'success',
                        CrmStatus::Failed => 'danger',
                    })
                    ->sortable(),
                TextColumn::make('submitted_at')
                    ->dateTime()
                    ->sortable()
                    ->since(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('inspectFailedPush')
                    ->label('Inspect Failed Push')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->visible(fn (Submission $record): bool => $record->latestSyncAttempt?->status === SyncAttemptStatus::Failed)
                    ->url(fn (Submission $record): string => CrmSyncAttemptResource::getUrl('view', ['record' => $record->latestSyncAttempt])),
            ])
            ->defaultSort('submitted_at', 'desc');
    }
}
