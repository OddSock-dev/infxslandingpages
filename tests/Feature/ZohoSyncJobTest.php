<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Actions\Zoho\SubmitLeadToZohoAction;
use App\Enums\CrmStatus;
use App\Jobs\SyncSubmissionToZohoJob;
use App\Models\Submission;
use App\Models\ZohoCredential;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Livewire\Component;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Tests\TestCase;

class ZohoSyncJobTest extends TestCase
{
    use RefreshDatabase;

    private function trialUrlFor(string $pageKey): string
    {
        return match ($pageKey) {
            'zoho_marketing_plus' => 'https://store.zoho.com/ResellerCustomerSignUp.do?id=cb03293c4f696fa1058bd992189acdee',
            'zoho_workplace' => 'https://store.zoho.com/ResellerCustomerSignUp.do?id=2afc43fa6ca997e0dae84629788ad5e8',
            default => 'https://store.zoho.com/ResellerCustomerSignUp.do?id=b90ffafff590634f12c003a7325340d7',
        };
    }

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.zoho.client_id', 'test-client-id');
        config()->set('services.zoho.client_secret', 'test-client-secret');
        config()->set('services.zoho.api_domain', 'https://www.zohoapis.test');
        config()->set('services.zoho.accounts_url', 'https://accounts.zoho.test');

        ZohoCredential::set(ZohoCredential::CLIENT_ID, 'test-client-id');
        ZohoCredential::set(ZohoCredential::CLIENT_SECRET, 'test-client-secret');
        ZohoCredential::set(ZohoCredential::API_DOMAIN, 'https://www.zohoapis.test');
        ZohoCredential::set(ZohoCredential::ACCOUNTS_URL, 'https://accounts.zoho.test');
        ZohoCredential::set(ZohoCredential::AUTHORIZATION_URL, 'https://accounts.zoho.test/oauth/v2/auth');
        ZohoCredential::set(ZohoCredential::ACCESS_TOKEN_URL, 'https://accounts.zoho.test/oauth/v2/token');
        ZohoCredential::set(ZohoCredential::ACCESS_TOKEN, 'valid-access-token', CarbonImmutable::now()->addHour());

        Http::preventStrayRequests();
    }

    public function test_job_is_dispatched_after_a_public_submission_is_created(): void
    {
        Queue::fake();

        /** @var Testable<Component> $component */
        $component = Livewire::test('marketing.product-lead', [
            'pageKey' => 'zoho_one',
            'productName' => 'Zoho One',
            'trialUrl' => $this->trialUrlFor('zoho_one'),
        ]);

        $component
            ->set('companySizeBand', '11_50')
            ->set('implementationTimeline', 'this_quarter')
            ->set('currentEnvironment', 'multiple_line_of_business_tools')
            ->set('priorityOutcome', 'shared_operating_system')
            ->call('completeQualification')
            ->call('requestConsultation')
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->call('submit')
            ->assertRedirect(route('thanks'));

        Queue::assertPushed(SyncSubmissionToZohoJob::class);
    }

    public function test_handle_syncs_submission_to_zoho(): void
    {
        $submission = Submission::factory()->create();

        Http::fake([
            'https://www.zohoapis.test/crm/v8/Leads' => Http::response([
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

        Http::fake();

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
            'https://www.zohoapis.test/crm/v8/Leads' => Http::response(['message' => 'Internal Server Error'], 500),
        ]);

        $job = new SyncSubmissionToZohoJob($submission->id);

        $this->expectException(RequestException::class);
        $job->handle(app(SubmitLeadToZohoAction::class));

        $submission->refresh();
        $this->assertSame(CrmStatus::Failed, $submission->crm_status);
    }
}
