<?php

declare(strict_types=1);

namespace App\Actions\Zoho;

use App\Enums\CrmStatus;
use App\Enums\SyncAttemptStatus;
use App\Jobs\SyncSubmissionToZohoJob;
use App\Models\CrmSyncAttempt;
use App\Models\Submission;
use App\Support\SubmissionSyncRequeueResult;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RequeueSubmissionSyncAction
{
    private const REQUEUE_LOCK_SECONDS = 10;

    public function canRequeueAttempt(CrmSyncAttempt $attempt): bool
    {
        $attempt->loadMissing('submission.latestSyncAttempt');

        $submission = $attempt->submission;

        if ($submission === null) {
            return false;
        }

        $latestAttempt = $submission->latestSyncAttempt;

        return $attempt->status === SyncAttemptStatus::Failed
            && $submission->crm_status !== CrmStatus::Synced
            && $latestAttempt !== null
            && $latestAttempt->is($attempt);
    }

    public function canRequeueSubmission(Submission $submission): bool
    {
        if ($submission->crm_status === CrmStatus::Synced) {
            return false;
        }

        $submission->loadMissing('latestSyncAttempt');

        return $submission->latestSyncAttempt?->status !== SyncAttemptStatus::Success;
    }

    public function executeForAttempt(CrmSyncAttempt $attempt): SubmissionSyncRequeueResult
    {
        if (! $this->canRequeueAttempt($attempt)) {
            return SubmissionSyncRequeueResult::warning(
                'Only the latest failed CRM push can be requeued from this screen.',
            );
        }

        $submission = $attempt->submission;

        if ($submission === null) {
            return SubmissionSyncRequeueResult::warning('The related submission could not be loaded for this CRM push.');
        }

        return $this->executeForSubmission($submission, $attempt);
    }

    public function executeForSubmission(
        Submission $submission,
        ?CrmSyncAttempt $attempt = null,
    ): SubmissionSyncRequeueResult {
        if ($submission->crm_status === CrmStatus::Synced) {
            return SubmissionSyncRequeueResult::warning('This submission is already synced to Zoho.');
        }

        $submission->loadMissing('latestSyncAttempt');

        if ($submission->latestSyncAttempt?->status === SyncAttemptStatus::Success) {
            return SubmissionSyncRequeueResult::warning(
                'The latest CRM push succeeded already, so nothing was requeued.',
            );
        }

        $lock = Cache::lock(
            sprintf('crm-sync-requeue:%d', $submission->id),
            self::REQUEUE_LOCK_SECONDS,
        );

        if (! $lock->get()) {
            return SubmissionSyncRequeueResult::warning(
                'A CRM retry is already being queued for this submission.',
            );
        }

        try {
            DB::transaction(function () use ($submission): void {
                $submission->update(['crm_status' => CrmStatus::Pending]);
            });

            SyncSubmissionToZohoJob::dispatch($submission->id);
        } finally {
            $lock->release();
        }

        Log::info('CRM sync requeued by operator.', [
            'submission_id' => $submission->id,
            'crm_sync_attempt_id' => $attempt?->id,
            'operator_user_id' => Auth::id(),
        ]);

        return SubmissionSyncRequeueResult::success(
            'CRM sync requeued. The submission is back in the pending sync queue.',
        );
    }
}
