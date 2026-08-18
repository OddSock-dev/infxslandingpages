<?php

declare(strict_types=1);

namespace App\Filament\Resources\CrmSyncAttempts\Pages;

use App\Actions\Zoho\RequeueSubmissionSyncAction;
use App\Filament\Resources\CrmSyncAttempts\CrmSyncAttemptResource;
use App\Filament\Resources\Submissions\SubmissionResource;
use App\Models\CrmSyncAttempt;
use App\Support\SubmissionSyncRequeueResult;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewCrmSyncAttempt extends ViewRecord
{
    protected static string $resource = CrmSyncAttemptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('viewSubmission')
                ->label('View Submission')
                ->icon('heroicon-o-document-text')
                ->url(fn (): string => SubmissionResource::getUrl('view', ['record' => $this->record()->submission_id])),
            Action::make('requeue')
                ->label('Requeue Sync')
                ->icon('heroicon-o-arrow-path')
                ->requiresConfirmation()
                ->modalDescription('This resets the submission to pending and queues a fresh Zoho retry.')
                ->visible(fn (): bool => app(RequeueSubmissionSyncAction::class)->canRequeueAttempt($this->record()))
                ->action(function (): void {
                    $this->sendNotification(app(RequeueSubmissionSyncAction::class)->executeForAttempt($this->record()));
                }),
        ];
    }

    private function record(): CrmSyncAttempt
    {
        $record = $this->getRecord();

        if (! $record instanceof CrmSyncAttempt) {
            throw new \RuntimeException(sprintf('Expected %s, received %s.', CrmSyncAttempt::class, $record::class));
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
