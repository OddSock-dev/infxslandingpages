<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Actions\Zoho\SubmitLeadToZohoAction;
use App\Enums\CrmStatus;
use App\Models\Submission;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Queued job that syncs a single submission to Zoho CRM.
 *
 * Uniqueness: only one job per submission_id is allowed in the queue at a
 * time (ShouldBeUniqueUntilProcessing). This prevents duplicate dispatch but
 * does NOT prevent duplicate Zoho leads if Zoho is called before the local
 * DB update completes — handled via idempotency guard on CrmStatus::Synced.
 */
class SyncSubmissionToZohoJob implements ShouldBeUniqueUntilProcessing, ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    /** @var list<int> */
    public array $backoff = [30, 60, 120];

    public function __construct(public readonly int $submissionId) {}

    public function uniqueId(): string
    {
        return (string) $this->submissionId;
    }

    public function handle(SubmitLeadToZohoAction $action): void
    {
        $submission = Submission::query()->findOrFail($this->submissionId);

        if ($submission->crm_status === CrmStatus::Synced) {
            return;
        }

        $action->execute($submission);
    }
}
