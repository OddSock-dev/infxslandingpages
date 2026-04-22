<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Actions\Zoho\SubmitLeadToZohoAction;
use App\Enums\CrmStatus;
use App\Enums\SyncAttemptStatus;
use App\Models\Submission;
use App\Models\ZohoCredential;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SubmitLeadToZohoActionTest extends TestCase
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

    public function test_creates_lead_in_zoho_and_marks_submission_synced(): void
    {
        $submission = Submission::factory()->create();

        Http::fake([
            'https://www.zohoapis.test/crm/v2/Leads' => Http::response([
                'data' => [['code' => 'SUCCESS', 'details' => ['id' => '1234567890'], 'message' => 'record added', 'status' => 'success']],
            ], 201),
        ]);

        $action = app(SubmitLeadToZohoAction::class);
        $action->execute($submission);

        $submission->refresh();
        $this->assertSame(CrmStatus::Synced, $submission->crm_status);

        $attempt = $submission->syncAttempts()->latest()->first();
        $this->assertNotNull($attempt);
        $this->assertSame(SyncAttemptStatus::Success, $attempt->status);
        $this->assertNotNull($attempt->response_payload);
    }

    public function test_records_failed_attempt_on_http_error(): void
    {
        $submission = Submission::factory()->create();

        Http::fake([
            'https://www.zohoapis.test/crm/v2/Leads' => Http::response(['message' => 'Server Error'], 500),
        ]);

        $action = app(SubmitLeadToZohoAction::class);

        try {
            $action->execute($submission);
            $this->fail('Expected RequestException was not thrown.');
        } catch (RequestException $e) {
            // expected
        }

        $submission->refresh();
        $this->assertSame(CrmStatus::Failed, $submission->crm_status);

        $attempt = $submission->syncAttempts()->latest()->first();
        $this->assertNotNull($attempt);
        $this->assertSame(SyncAttemptStatus::Failed, $attempt->status);
        $this->assertSame('API_ERROR', $attempt->error_code);
    }

    public function test_refreshes_expired_access_token_before_api_call(): void
    {
        ZohoCredential::clear('access_token');
        ZohoCredential::set('refresh_token', 'valid-refresh-token');

        $submission = Submission::factory()->create();

        Http::fake([
            'https://accounts.zoho.test/oauth/v2/token' => Http::response([
                'access_token' => 'fresh-access-token',
                'expires_in' => 3600,
            ], 200),
            'https://www.zohoapis.test/crm/v2/Leads' => Http::response([
                'data' => [['code' => 'SUCCESS', 'details' => ['id' => '99'], 'message' => 'record added', 'status' => 'success']],
            ], 201),
        ]);

        $action = app(SubmitLeadToZohoAction::class);
        $action->execute($submission);

        $submission->refresh();
        $this->assertSame(CrmStatus::Synced, $submission->crm_status);
        $this->assertSame('fresh-access-token', ZohoCredential::get('access_token'));
    }

    public function test_request_payload_is_stored_on_the_attempt(): void
    {
        $submission = Submission::factory()->create([
            'product_key' => 'zoho_one',
            'pii_json' => ['name' => 'Jane Doe', 'email' => 'jane@example.com', 'phone' => null, 'company' => null],
        ]);

        Http::fake([
            'https://www.zohoapis.test/crm/v2/Leads' => Http::response(['data' => [['code' => 'SUCCESS']]], 201),
        ]);

        $action = app(SubmitLeadToZohoAction::class);
        $action->execute($submission);

        $attempt = $submission->syncAttempts()->latest()->first();
        $this->assertNotNull($attempt);
        $this->assertArrayHasKey('data', $attempt->request_payload);
    }
}
