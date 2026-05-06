<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Actions\Zoho\RequeueSubmissionSyncAction;
use App\Enums\CrmStatus;
use App\Jobs\SyncSubmissionToZohoJob;
use App\Models\CrmSyncAttempt;
use App\Models\Submission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class RequeueSubmissionSyncActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_requeues_the_latest_failed_submission_sync(): void
    {
        Queue::fake();

        $submission = Submission::factory()->failed()->create();
        $attempt = CrmSyncAttempt::factory()
            ->for($submission)
            ->failed('HTTP_500_SERVER_ERROR', 'Server error')
            ->create();

        $result = app(RequeueSubmissionSyncAction::class)->executeForAttempt($attempt);

        $this->assertTrue($result->queued);
        $this->assertSame('success', $result->status);

        $submission->refresh();
        $this->assertSame(CrmStatus::Pending, $submission->crm_status);
        Queue::assertPushed(SyncSubmissionToZohoJob::class, fn (SyncSubmissionToZohoJob $job): bool => $job->submissionId === $submission->id);
    }

    public function test_skips_historical_failed_attempts_that_have_already_been_resolved(): void
    {
        Queue::fake();

        $submission = Submission::factory()->synced()->create();
        $historicalAttempt = CrmSyncAttempt::factory()
            ->for($submission)
            ->failed('HTTP_500_SERVER_ERROR', 'Old failure')
            ->create(['attempted_at' => now()->subMinutes(10)]);

        CrmSyncAttempt::factory()
            ->for($submission)
            ->success()
            ->create(['attempted_at' => now()]);

        $result = app(RequeueSubmissionSyncAction::class)->executeForAttempt($historicalAttempt);

        $this->assertFalse($result->queued);
        $this->assertSame('warning', $result->status);
        $submission->refresh();
        $this->assertSame(CrmStatus::Synced, $submission->crm_status);
        Queue::assertNothingPushed();
    }

    public function test_does_not_requeue_a_submission_that_has_already_synced(): void
    {
        Queue::fake();

        $submission = Submission::factory()->synced()->create();

        $result = app(RequeueSubmissionSyncAction::class)->executeForSubmission($submission);

        $this->assertFalse($result->queued);
        $this->assertSame('warning', $result->status);
        Queue::assertNothingPushed();
    }
}
