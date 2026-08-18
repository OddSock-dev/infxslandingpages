<?php

declare(strict_types=1);

namespace App\Filament\Resources\CrmSyncAttempts\Tables;

use App\Actions\Zoho\RequeueSubmissionSyncAction;
use App\Enums\CrmStatus;
use App\Enums\SyncAttemptStatus;
use App\Filament\Resources\Submissions\SubmissionResource;
use App\Models\CrmSyncAttempt;
use App\Support\SubmissionSyncRequeueResult;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class CrmSyncAttemptsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with(['submission.latestSyncAttempt']))
            ->columns([
                TextColumn::make('id')
                    ->label('Attempt')
                    ->sortable(),
                TextColumn::make('submission_id')
                    ->label('Submission')
                    ->url(fn (CrmSyncAttempt $record): string => SubmissionResource::getUrl('view', ['record' => $record->submission_id]))
                    ->sortable(),
                TextColumn::make('submission.product_key')
                    ->label('Product')
                    ->sortable(),
                TextColumn::make('submission.crm_status')
                    ->label('Submission Status')
                    ->badge()
                    ->color(fn (CrmStatus $state): string => match ($state) {
                        CrmStatus::Pending => 'warning',
                        CrmStatus::Synced => 'success',
                        CrmStatus::Failed => 'danger',
                    })
                    ->toggleable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (SyncAttemptStatus $state): string => match ($state) {
                        SyncAttemptStatus::Pending => 'warning',
                        SyncAttemptStatus::Success => 'success',
                        SyncAttemptStatus::Failed => 'danger',
                    })
                    ->sortable(),
                TextColumn::make('operator_state')
                    ->label('Operator State')
                    ->badge()
                    ->state(fn (CrmSyncAttempt $record): string => self::operatorState($record))
                    ->color(fn (string $state): string => match ($state) {
                        'Needs retry' => 'danger',
                        'Resolved' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('provider')
                    ->searchable(),
                TextColumn::make('error_code')
                    ->badge()
                    ->color('danger')
                    ->toggleable(),
                TextColumn::make('attempted_at')
                    ->dateTime()
                    ->sortable()
                    ->since(),
                TextColumn::make('error_message')
                    ->limit(80)
                    ->tooltip(fn (?string $state): ?string => $state)
                    ->wrap()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(self::statusOptions()),
                SelectFilter::make('error_code')
                    ->options(fn (): array => CrmSyncAttempt::query()
                        ->whereNotNull('error_code')
                        ->distinct()
                        ->orderBy('error_code')
                        ->pluck('error_code', 'error_code')
                        ->all()),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('viewSubmission')
                    ->label('View Submission')
                    ->icon('heroicon-o-document-text')
                    ->url(fn (CrmSyncAttempt $record): string => SubmissionResource::getUrl('view', ['record' => $record->submission_id])),
                Action::make('requeue')
                    ->label('Requeue Sync')
                    ->icon('heroicon-o-arrow-path')
                    ->requiresConfirmation()
                    ->modalDescription('This resets the submission to pending and safely queues a fresh Zoho sync attempt.')
                    ->visible(fn (CrmSyncAttempt $record): bool => app(RequeueSubmissionSyncAction::class)->canRequeueAttempt($record))
                    ->action(function (CrmSyncAttempt $record): void {
                        self::sendNotification(app(RequeueSubmissionSyncAction::class)->executeForAttempt($record));
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('requeueFailed')
                        ->label('Requeue selected failures')
                        ->icon('heroicon-o-arrow-path')
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records): void {
                            $requeueAction = app(RequeueSubmissionSyncAction::class);
                            $queuedCount = 0;
                            $skippedCount = 0;

                            foreach ($records as $record) {
                                if (! $record instanceof CrmSyncAttempt) {
                                    continue;
                                }

                                $result = $requeueAction->executeForAttempt($record);

                                if ($result->queued) {
                                    $queuedCount++;

                                    continue;
                                }

                                $skippedCount++;
                            }

                            self::sendBulkNotification($queuedCount, $skippedCount);
                        }),
                ]),
            ])
            ->defaultSort('attempted_at', 'desc');
    }

    /**
     * @return array<string, string>
     */
    private static function statusOptions(): array
    {
        $options = [];

        foreach (SyncAttemptStatus::cases() as $status) {
            $options[$status->value] = $status->name;
        }

        return $options;
    }

    private static function operatorState(CrmSyncAttempt $record): string
    {
        $latestAttempt = $record->submission?->latestSyncAttempt;

        if ($record->submission === null) {
            return 'Submission missing';
        }

        if ($record->submission->crm_status === CrmStatus::Synced) {
            return 'Resolved';
        }

        if ($latestAttempt !== null && $latestAttempt->is($record) && $record->status === SyncAttemptStatus::Failed) {
            return 'Needs retry';
        }

        return 'History';
    }

    private static function sendBulkNotification(int $queuedCount, int $skippedCount): void
    {
        $notification = Notification::make();

        if ($queuedCount > 0) {
            $notification
                ->title(
                    sprintf(
                        'Queued %d CRM sync attempt%s. Skipped %d selection%s.',
                        $queuedCount,
                        $queuedCount === 1 ? '' : 's',
                        $skippedCount,
                        $skippedCount === 1 ? '' : 's',
                    ),
                )
                ->success()
                ->send();

            return;
        }

        $notification
            ->title('No selected rows were eligible for requeueing.')
            ->warning()
            ->send();
    }

    private static function sendNotification(SubmissionSyncRequeueResult $result): void
    {
        $notification = Notification::make()->title($result->message);

        match ($result->status) {
            'danger' => $notification->danger(),
            'warning' => $notification->warning(),
            default => $notification->success(),
        };

        $notification->send();
    }
}
