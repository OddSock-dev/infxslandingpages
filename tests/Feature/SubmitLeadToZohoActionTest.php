<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Actions\Zoho\SubmitLeadToZohoAction;
use App\Enums\CrmStatus;
use App\Enums\SyncAttemptStatus;
use App\Models\Journey;
use App\Models\JourneyAnswer;
use App\Models\Submission;
use App\Models\ZohoCredential;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
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

        ZohoCredential::set(ZohoCredential::CLIENT_ID, 'test-client-id');
        ZohoCredential::set(ZohoCredential::CLIENT_SECRET, 'test-client-secret');
        ZohoCredential::set(ZohoCredential::API_DOMAIN, 'https://www.zohoapis.test');
        ZohoCredential::set(ZohoCredential::ACCOUNTS_URL, 'https://accounts.zoho.test');
        ZohoCredential::set(ZohoCredential::AUTHORIZATION_URL, 'https://accounts.zoho.test/oauth/v2/auth');
        ZohoCredential::set(ZohoCredential::ACCESS_TOKEN_URL, 'https://accounts.zoho.test/oauth/v2/token');
        ZohoCredential::set(ZohoCredential::ACCESS_TOKEN, 'valid-access-token', CarbonImmutable::now()->addHour());

        Http::preventStrayRequests();
    }

    public function test_creates_lead_in_zoho_and_marks_submission_synced(): void
    {
        $submission = Submission::factory()->create();

        Http::fake([
            'https://www.zohoapis.test/crm/v8/Leads' => Http::response([
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

    public function test_sends_zoho_oauth_token_authorization_header_to_crm(): void
    {
        $submission = Submission::factory()->create();

        Http::fake([
            'https://www.zohoapis.test/crm/v8/Leads' => Http::response([
                'data' => [['code' => 'SUCCESS']],
            ], 201),
        ]);

        app(SubmitLeadToZohoAction::class)->execute($submission);

        Http::assertSent(function (Request $request): bool {
            return $request->url() === 'https://www.zohoapis.test/crm/v8/Leads'
                && $request->hasHeader('Authorization', 'Zoho-oauthtoken valid-access-token');
        });
    }

    public function test_records_failed_attempt_on_http_error(): void
    {
        $submission = Submission::factory()->create();

        Http::fake([
            'https://www.zohoapis.test/crm/v8/Leads' => Http::response(['message' => 'Server Error'], 500),
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
        $this->assertSame('HTTP_5XX_SERVER_ERROR', $attempt->error_code);
        $this->assertSame('HTTP 500: Server Error', $attempt->error_message);
        $this->assertSame(['message' => 'Server Error'], $attempt->response_payload);
    }

    public function test_refreshes_expired_access_token_before_api_call(): void
    {
        ZohoCredential::clear(ZohoCredential::ACCESS_TOKEN);
        ZohoCredential::set(ZohoCredential::REFRESH_TOKEN, 'valid-refresh-token');

        $submission = Submission::factory()->create();

        Http::fake([
            'https://accounts.zoho.test/oauth/v2/token' => Http::response([
                'access_token' => 'fresh-access-token',
                'expires_in' => 3600,
            ], 200),
            'https://www.zohoapis.test/crm/v8/Leads' => Http::response([
                'data' => [['code' => 'SUCCESS', 'details' => ['id' => '99'], 'message' => 'record added', 'status' => 'success']],
            ], 201),
        ]);

        $action = app(SubmitLeadToZohoAction::class);
        $action->execute($submission);

        $submission->refresh();
        $this->assertSame(CrmStatus::Synced, $submission->crm_status);
        $this->assertSame('fresh-access-token', ZohoCredential::get(ZohoCredential::ACCESS_TOKEN));
    }

    public function test_retries_the_lead_request_after_zoho_reports_an_expired_token(): void
    {
        ZohoCredential::set(ZohoCredential::REFRESH_TOKEN, 'rotatable-refresh-token');

        $submission = Submission::factory()->create();

        Http::fake([
            'https://accounts.zoho.test/oauth/v2/token' => Http::response([
                'access_token' => 'replacement-access-token',
                'refresh_token' => 'replacement-refresh-token',
                'expires_in' => 3600,
            ], 200),
            'https://www.zohoapis.test/crm/v8/Leads' => Http::sequence()
                ->push(['message' => 'Unauthorized'], 401)
                ->push([
                    'data' => [[
                        'code' => 'SUCCESS',
                        'details' => ['id' => '1234567890'],
                        'message' => 'record added',
                        'status' => 'success',
                    ]],
                ], 201),
        ]);

        $action = app(SubmitLeadToZohoAction::class);
        $action->execute($submission);

        $submission->refresh();
        $this->assertSame(CrmStatus::Synced, $submission->crm_status);
        $this->assertSame('replacement-access-token', ZohoCredential::get(ZohoCredential::ACCESS_TOKEN));
        $this->assertSame('replacement-refresh-token', ZohoCredential::get(ZohoCredential::REFRESH_TOKEN));

        Http::assertSentCount(3);
    }

    public function test_records_failed_attempt_when_refresh_token_exchange_fails(): void
    {
        ZohoCredential::clear(ZohoCredential::ACCESS_TOKEN);
        ZohoCredential::set(ZohoCredential::REFRESH_TOKEN, 'valid-refresh-token');

        $submission = Submission::factory()->create();

        Http::fake([
            'https://accounts.zoho.test/oauth/v2/token' => Http::response(['message' => 'Server Error'], 500),
        ]);

        $action = app(SubmitLeadToZohoAction::class);

        try {
            $action->execute($submission);
            $this->fail('Expected RequestException was not thrown.');
        } catch (RequestException $e) {
            $this->assertStringContainsString('500', $e->getMessage());
        }

        $submission->refresh();
        $this->assertSame(CrmStatus::Failed, $submission->crm_status);

        $attempt = $submission->syncAttempts()->latest()->first();
        $this->assertNotNull($attempt);
        $this->assertSame(SyncAttemptStatus::Failed, $attempt->status);
        $this->assertSame('HTTP_5XX_SERVER_ERROR', $attempt->error_code);
        $this->assertIsString($attempt->error_message);
        $this->assertStringContainsString('500', $attempt->error_message);
        $this->assertSame(['message' => 'Server Error'], $attempt->response_payload);
    }

    public function test_records_failed_attempt_when_no_refresh_token_is_available(): void
    {
        ZohoCredential::clear(ZohoCredential::ACCESS_TOKEN);
        ZohoCredential::clear(ZohoCredential::REFRESH_TOKEN);

        $submission = Submission::factory()->create();

        Http::fake();

        $action = app(SubmitLeadToZohoAction::class);

        try {
            $action->execute($submission);
            $this->fail('Expected RuntimeException was not thrown.');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('No Zoho refresh token found', $e->getMessage());
        }

        $submission->refresh();
        $this->assertSame(CrmStatus::Failed, $submission->crm_status);

        $attempt = $submission->syncAttempts()->latest()->first();
        $this->assertNotNull($attempt);
        $this->assertSame(SyncAttemptStatus::Failed, $attempt->status);
        $this->assertSame('SYNC_ERROR', $attempt->error_code);
        $this->assertIsString($attempt->error_message);
        $this->assertStringContainsString('No Zoho refresh token found', $attempt->error_message);

        Http::assertNothingSent();
    }

    public function test_request_payload_is_stored_on_the_attempt(): void
    {
        $submission = Submission::factory()->create([
            'product_key' => 'zoho_one',
            'pii_json' => ['name' => 'Jane Doe', 'email' => 'jane@example.com', 'phone' => null, 'company' => null],
        ]);

        Http::fake([
            'https://www.zohoapis.test/crm/v8/Leads' => Http::response(['data' => [['code' => 'SUCCESS']]], 201),
        ]);

        $action = app(SubmitLeadToZohoAction::class);
        $action->execute($submission);

        $attempt = $submission->syncAttempts()->latest()->first();
        $this->assertNotNull($attempt);
        $this->assertArrayHasKey('data', $attempt->request_payload);
    }

    public function test_normalizes_whitespace_in_stored_email_before_calling_zoho(): void
    {
        $submission = Submission::factory()->create([
            'pii_json' => [
                'name' => 'Jane Doe',
                'email' => ' jane @example .com ',
                'phone' => null,
                'company' => null,
            ],
        ]);

        Http::fake([
            'https://www.zohoapis.test/crm/v8/Leads' => Http::response(['data' => [['code' => 'SUCCESS']]], 201),
        ]);

        app(SubmitLeadToZohoAction::class)->execute($submission);

        Http::assertSent(function (Request $request): bool {
            /** @var array<string, mixed> $payload */
            $payload = json_decode($request->body(), true, 512, JSON_THROW_ON_ERROR);

            return data_get($payload, 'data.0.Email') === 'jane@example.com';
        });
    }

    public function test_records_failed_attempt_without_calling_zoho_when_stored_email_is_invalid(): void
    {
        $submission = Submission::factory()->create([
            'pii_json' => [
                'name' => 'Jane Doe',
                'email' => 'not-an-email',
                'phone' => null,
                'company' => null,
            ],
        ]);

        Http::fake();

        $action = app(SubmitLeadToZohoAction::class);

        try {
            $action->execute($submission);
            $this->fail('Expected InvalidArgumentException was not thrown.');
        } catch (\InvalidArgumentException $exception) {
            $this->assertSame('Submission email is invalid for Zoho CRM.', $exception->getMessage());
        }

        Http::assertNothingSent();

        $submission->refresh();
        $this->assertSame(CrmStatus::Failed, $submission->crm_status);

        $attempt = $submission->syncAttempts()->latest()->first();
        $this->assertNotNull($attempt);
        $this->assertSame(SyncAttemptStatus::Failed, $attempt->status);
        $this->assertSame('INVALID_SUBMISSION_DATA', $attempt->error_code);
        $this->assertSame('Submission email is invalid for Zoho CRM.', $attempt->error_message);
        $this->assertSame('not-an-email', data_get($attempt->request_payload, 'data.0.Email'));
    }

    public function test_sends_the_expected_product_page_payload_to_zoho(): void
    {
        $submission = Submission::factory()->create([
            'product_key' => 'zoho_one',
            'page_key' => 'zoho_one',
            'pii_json' => [
                'name' => 'Jane Doe',
                'email' => 'jane@example.com',
                'phone' => '+27211234567',
                'company' => '',
            ],
            'meta_json' => [
                'company_size_band' => '11_50',
                'implementation_timeline' => 'this_quarter',
                'current_environment' => 'multiple_line_of_business_tools',
                'priority_outcome' => 'shared_operating_system',
            ],
        ]);

        Http::fake([
            'https://www.zohoapis.test/crm/v8/Leads' => Http::response(['data' => [['code' => 'SUCCESS']]], 201),
        ]);

        app(SubmitLeadToZohoAction::class)->execute($submission);

        Http::assertSent(function (Request $request): bool {
            if ($request->url() !== 'https://www.zohoapis.test/crm/v8/Leads') {
                return false;
            }

            /** @var array<string, mixed> $payload */
            $payload = json_decode($request->body(), true, 512, JSON_THROW_ON_ERROR);
            /** @var array<string, mixed>|null $lead */
            $lead = data_get($payload, 'data.0');

            if (! is_array($lead)) {
                return false;
            }

            return $lead === [
                'Owner' => [
                    'name' => 'Alex Ndhlovu',
                    'id' => '2960730000120595001',
                    'email' => 'salcorporate@infinitybrands.co.za',
                ],
                'Lead_Stage' => '1. New (Important)',
                'Lead_Status' => 'New',
                'Interest' => 'INFX : ZOHO (Implementation)',
                'Brand' => 'INFX: Zoho',
                'Lead_Source' => 'INFX Zoho Magnet',
                'Franchise' => 'INFX Solutions',
                'Product' => 'Corporate (COR)',
                'Sales_Department' => 'SAL COR: Product Sales',
                'First_Name' => 'Jane',
                'Last_Name' => 'Doe',
                'Email' => 'jane@example.com',
                'Mobile' => '+27211234567',
                'Phone' => '+27211234567',
                'Tag' => [
                    ['name' => 'INFXS'],
                    ['name' => 'INFX'],
                ],
                'Description' => implode("\n\n", [
                    'Product: Zoho One',
                    implode("\n", [
                        'Product page answers',
                        '1. How many people will use this? 11-50 people',
                        '2. When would you like to get started? This quarter',
                        '3. What does the current setup look like? Too many separate business tools are in use',
                        '4. What is the main result you want from this product? One connected setup across the business',
                        'Contact details: Jane Doe / jane@example.com / +27211234567',
                    ]),
                ]),
            ];
        });
    }

    public function test_sends_homepage_and_product_answers_in_the_description_when_a_journey_exists(): void
    {
        $journey = Journey::factory()->routed('zoho_one')->create();

        JourneyAnswer::query()->create([
            'journey_id' => $journey->id,
            'step_key' => 'diagnostic_1',
            'field_key' => 'primary_goal',
            'value' => 'unify_operations',
        ]);
        JourneyAnswer::query()->create([
            'journey_id' => $journey->id,
            'step_key' => 'diagnostic_2',
            'field_key' => 'biggest_gap',
            'value' => 'manual_handoffs',
        ]);
        JourneyAnswer::query()->create([
            'journey_id' => $journey->id,
            'step_key' => 'diagnostic_3',
            'field_key' => 'team_shape',
            'value' => 'multi_department_growth',
        ]);
        JourneyAnswer::query()->create([
            'journey_id' => $journey->id,
            'step_key' => 'diagnostic_4',
            'field_key' => 'timeline',
            'value' => 'this_quarter',
        ]);

        $submission = Submission::factory()->create([
            'journey_id' => $journey->id,
            'product_key' => 'zoho_one',
            'page_key' => 'zoho_one',
            'pii_json' => [
                'name' => 'Robin Myndos',
                'email' => 'info@myndos.co.za',
                'phone' => '0763029206',
                'company' => 'Myndos',
            ],
            'meta_json' => [
                'company_size_band' => '11_50',
                'implementation_timeline' => 'this_quarter',
                'current_environment' => 'multiple_line_of_business_tools',
                'priority_outcome' => 'shared_operating_system',
            ],
        ]);

        Http::fake([
            'https://www.zohoapis.test/crm/v8/Leads' => Http::response(['data' => [['code' => 'SUCCESS']]], 201),
        ]);

        app(SubmitLeadToZohoAction::class)->execute($submission);

        Http::assertSent(function (Request $request): bool {
            /** @var array<string, mixed> $payload */
            $payload = json_decode($request->body(), true, 512, JSON_THROW_ON_ERROR);
            /** @var array<string, mixed>|null $lead */
            $lead = data_get($payload, 'data.0');

            if (! is_array($lead)) {
                return false;
            }

            return ($lead['Description'] ?? null) === implode("\n\n", [
                'Product: Zoho One',
                implode("\n", [
                    'Lead magnet answers',
                    '1. What do you need to improve? Run the business from one connected set of tools',
                    '2. What is slowing you down? Too much manual work between teams',
                    '3. What does your team look like? A growing team across several departments',
                    '4. When do you want this live? This quarter',
                    '5. Where should we send it? Robin Myndos / info@myndos.co.za / 0763029206 / Myndos',
                ]),
                implode("\n", [
                    'Product page answers',
                    '1. How many people will use this? 11-50 people',
                    '2. When would you like to get started? This quarter',
                    '3. What does the current setup look like? Too many separate business tools are in use',
                    '4. What is the main result you want from this product? One connected setup across the business',
                ]),
            ]);
        });
    }

    public function test_redacts_pii_inside_recorded_error_messages(): void
    {
        $submission = Submission::factory()->create([
            'pii_json' => [
                'name' => 'Jane Doe',
                'email' => 'jane@example.com',
                'phone' => '+27 82 555 1212',
                'company' => 'Acme Pty Ltd',
            ],
        ]);

        Http::fake([
            'https://www.zohoapis.test/crm/v8/Leads' => Http::response([
                'message' => 'Lead jane@example.com with phone +27 82 555 1212 was rejected.',
            ], 422),
        ]);

        $action = app(SubmitLeadToZohoAction::class);

        try {
            $action->execute($submission);
            $this->fail('Expected RequestException was not thrown.');
        } catch (RequestException $e) {
            $this->assertStringContainsString('422', $e->getMessage());
        }

        $attempt = $submission->syncAttempts()->latest()->first();
        $this->assertNotNull($attempt);
        $this->assertSame('HTTP_422_VALIDATION', $attempt->error_code);
        $this->assertIsString($attempt->error_message);
        $this->assertStringNotContainsString('jane@example.com', $attempt->error_message);
        $this->assertStringNotContainsString('+27 82 555 1212', $attempt->error_message);
        $this->assertStringContainsString('j***@example.com', $attempt->error_message);
        $this->assertStringContainsString('*******1212', $attempt->error_message);
    }
}
