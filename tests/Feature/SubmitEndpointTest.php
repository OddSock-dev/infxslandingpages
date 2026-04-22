<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\CrmStatus;
use App\Enums\JourneyStatus;
use App\Enums\PageType;
use App\Models\Journey;
use App\Models\Page;
use App\Models\Submission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubmitEndpointTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Page::factory()->create([
            'page_key' => 'zoho_one',
            'page_type' => PageType::Product,
            'slug' => '/products/zoho-one',
            'is_active' => true,
        ]);
    }

    /**
     * @return array{product_key: string, name: string, email: string, phone: string, company: string, journey_token?: string}
     */
    private function submitPayload(string $productKey = 'zoho_one', ?string $journeyToken = null): array
    {
        $payload = [
            'product_key' => $productKey,
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '+27211234567',
            'company' => 'ACME Ltd',
        ];

        if ($journeyToken !== null) {
            $payload['journey_token'] = $journeyToken;
        }

        return $payload;
    }

    public function test_creates_a_submission_without_a_journey_token(): void
    {
        $response = $this->postJson('/api/funnel/submit', $this->submitPayload());

        $response->assertCreated()->assertJsonPath('message', 'Submission received.');

        $submission = Submission::firstOrFail();
        $this->assertSame('zoho_one', $submission->product_key);
        $this->assertSame(CrmStatus::Pending, $submission->crm_status);
        $this->assertNull($submission->journey_id);
    }

    public function test_creates_a_submission_linked_to_a_valid_journey_token(): void
    {
        $journey = Journey::factory()->routed('zoho_one')->create();

        $response = $this->postJson('/api/funnel/submit', $this->submitPayload('zoho_one', $journey->journey_token));
        $response->assertCreated();

        $submission = Submission::firstOrFail();
        $this->assertSame($journey->id, $submission->journey_id);

        $journey->refresh();
        $this->assertSame(JourneyStatus::Submitted, $journey->status);
    }

    public function test_merges_journey_utm_data_into_submission_meta_json(): void
    {
        $journey = Journey::factory()->routed('zoho_one')->create([
            'utm_source' => 'facebook',
            'utm_campaign' => 'q1-promo',
        ]);

        $this->postJson('/api/funnel/submit', $this->submitPayload('zoho_one', $journey->journey_token))
            ->assertCreated();

        $meta = Submission::firstOrFail()->meta_json;
        $this->assertSame('facebook', $meta['utm_source']);
        $this->assertSame('q1-promo', $meta['utm_campaign']);
    }

    public function test_returns_422_for_an_expired_journey_token(): void
    {
        $journey = Journey::factory()->expired()->create();

        $this->postJson('/api/funnel/submit', $this->submitPayload('zoho_one', $journey->journey_token))
            ->assertUnprocessable()
            ->assertJsonPath('message', 'Journey token is invalid or expired.');
    }

    public function test_returns_409_when_the_journey_has_already_been_submitted(): void
    {
        $journey = Journey::factory()->routed('zoho_one')->submitted()->create();

        $this->postJson('/api/funnel/submit', $this->submitPayload('zoho_one', $journey->journey_token))
            ->assertConflict()
            ->assertJsonPath('message', 'This journey has already been submitted.');
    }

    public function test_returns_422_when_product_key_does_not_match_the_journey(): void
    {
        Page::factory()->create([
            'page_key' => 'zoho_workplace',
            'page_type' => PageType::Product,
            'slug' => '/products/zoho-workplace',
            'is_active' => true,
        ]);

        $journey = Journey::factory()->routed('zoho_one')->create();

        $this->postJson('/api/funnel/submit', $this->submitPayload('zoho_workplace', $journey->journey_token))
            ->assertUnprocessable()
            ->assertJsonPath('message', 'The product key does not match the journey.');
    }

    public function test_validates_required_fields(): void
    {
        $this->postJson('/api/funnel/submit', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['product_key', 'name', 'email']);
    }

    public function test_rejects_an_invalid_email_address(): void
    {
        $payload = $this->submitPayload();
        $payload['email'] = 'not-an-email';

        $this->postJson('/api/funnel/submit', $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_rejects_an_inactive_product_page(): void
    {
        Page::factory()->create([
            'page_key' => 'zoho_inactive',
            'page_type' => PageType::Product,
            'slug' => '/products/zoho-inactive',
            'is_active' => false,
        ]);

        $this->postJson('/api/funnel/submit', $this->submitPayload('zoho_inactive'))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['product_key']);
    }
}
