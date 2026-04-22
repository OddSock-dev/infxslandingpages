<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Actions\Zoho\SubmitLeadToZohoAction;
use App\Enums\CrmStatus;
use App\Enums\PageType;
use App\Jobs\SyncSubmissionToZohoJob;
use App\Models\Page;
use App\Models\Submission;
use App\Models\ZohoCredential;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ZohoSyncJobTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.zoho.client_id', 'test-client-id');
        config()->set('services.zoho.client_secret', 'test-client-secret');
        config()->set('services.zoho.api_domain', 'https://www.zohoapis.test');
        config()->set('services.zoho.accounts_url', 'https://accounts.zoho.test');

        ZohoCredential::set('access_token', 'valid-access-token', CarbonImmutable::now()->addHour());

        Http::preventStrayRequests();
    }

    public function test_job_is_dispatched_after_submission_is_created(): void
    {
        Queue::fake();

        Page::factory()->create([
            'page_type' => PageType::Product,
            'page_key' => 'zoho_one',
            'slug' => 'zoho-one',
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/funnel/submit', [
            'product_key' => 'zoho_one',
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $response->assertCreated();

        Queue::assertPushed(SyncSubmissionToZohoJob::class);
    }

    public function test_handle_syncs_submission_to_zoho(): void
    {
        $submission = Submission::factory()->create();

        Http::fake([
            'https://www.zohoapis.test/crm/v2/Leads' => Http::response([
                'data' => [['code' => 'SUCCESS', 'details' => ['id' => '555'], 'message' => 'record added', 'status' => 'success']],
            ], 201),
        ]);

        $job = new SyncSubmissionToZohoJob($submission->id);
        $job->handle(app(SubmitLeadToZohoAction::class));

        $submission->refresh();
        $this->assertSame(CrmStatus::Synced, $submission->crm_status);
    }

    public function test_handle_skips_already_synced_submission(): void
    {
        $submission = Submission::factory()->synced()->create();

        Http::fake(); // should receive no calls

        $job = new SyncSubmissionToZohoJob($submission->id);
        $job->handle(app(SubmitLeadToZohoAction::class));

        Http::assertNothingSent();

        $submission->refresh();
        $this->assertSame(CrmStatus::Synced, $submission->crm_status);
    }

    public function test_handle_marks_submission_failed_on_api_error(): void
    {
        $submission = Submission::factory()->create();

        Http::fake([
            'https://www.zohoapis.test/crm/v2/Leads' => Http::response(['message' => 'Internal Server Error'], 500),
        ]);

        $job = new SyncSubmissionToZohoJob($submission->id);

        $this->expectException(RequestException::class);
        $job->handle(app(SubmitLeadToZohoAction::class));

        $submission->refresh();
        $this->assertSame(CrmStatus::Failed, $submission->crm_status);
    }
}
