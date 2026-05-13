<?php

declare(strict_types=1);

namespace App\Filament\Resources\Submissions\Pages;

use App\Actions\Zoho\RequeueSubmissionSyncAction;
use App\Enums\SyncAttemptStatus;
use App\Filament\Resources\CrmSyncAttempts\CrmSyncAttemptResource;
use App\Filament\Resources\Submissions\SubmissionResource;
use App\Models\CrmSyncAttempt;
use App\Models\Submission;
use App\Support\SubmissionSyncRequeueResult;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewSubmission extends ViewRecord
{
    protected static string $resource = SubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('inspectLatestFailure')
                ->label('Inspect Failed Push')
                ->icon('heroicon-o-exclamation-triangle')
                ->visible(fn (): bool => $this->latestFailedAttempt() !== null)
                ->url(fn (): string => CrmSyncAttemptResource::getUrl('view', ['record' => $this->latestFailedAttempt()])),
            Action::make('requeueSync')
                ->label('Requeue Sync')
                ->icon('heroicon-o-arrow-path')
                ->requiresConfirmation()
                ->modalDescription('This resets the submission to pending and safely queues a new Zoho push.')
                ->visible(fn (RequeueSubmissionSyncAction $requeueSubmissionSyncAction): bool => $requeueSubmissionSyncAction->canRequeueSubmission($this->record()))
                ->action(function (RequeueSubmissionSyncAction $requeueSubmissionSyncAction): void {
                    $this->sendNotification($requeueSubmissionSyncAction->executeForSubmission($this->record()));
                }),
        ];
    }

    private function latestFailedAttempt(): ?CrmSyncAttempt
    {
        $record = $this->record();
        $record->loadMissing('latestSyncAttempt');

        return $record->latestSyncAttempt?->status === SyncAttemptStatus::Failed
            ? $record->latestSyncAttempt
            : null;
    }

    private function record(): Submission
    {
        $record = $this->getRecord();

        if (! $record instanceof Submission) {
            throw new \RuntimeException(sprintf('Expected %s, received %s.', Submission::class, $record::class));
        }

        return $record;
    }

    private function sendNotification(SubmissionSyncRequeueResult $result): void
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
