<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JourneyStatus;
use App\Enums\PageType;
use App\Models\Journey;
use App\Models\JourneyAnswer;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QualifyEndpointTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Page::factory()->create([
            'page_key' => 'landing',
            'page_type' => PageType::Landing,
            'slug' => '/',
            'is_active' => true,
        ]);
    }

    /**
     * @return array{source_page_key: string, answers: list<array{step_key: string, field_key: string, value: string}>, consent: bool}
     */
    private function qualifyPayload(string $pageKey = 'landing'): array
    {
        return [
            'source_page_key' => $pageKey,
            'answers' => [
                ['step_key' => 'step_1', 'field_key' => 'business_size', 'value' => '11-50'],
            ],
            'consent' => true,
        ];
    }

    public function test_creates_journey_and_answers_returns_journey_token_and_product_key(): void
    {
        $response = $this->postJson('/api/funnel/qualify', $this->qualifyPayload());

        $response->assertCreated()
            ->assertJsonStructure(['journey_token', 'product_key'])
            ->assertJsonPath('product_key', 'zoho_one');

        /** @var string $token */
        $token = $response->json('journey_token');
        $this->assertSame(26, strlen($token));

        $journey = Journey::where('journey_token', $token)->firstOrFail();
        $this->assertSame(JourneyStatus::Routed, $journey->status);
        $this->assertSame('zoho_one', $journey->assigned_product_key);
        $this->assertSame('landing', $journey->source_page_key);
        $this->assertSame(1, JourneyAnswer::where('journey_id', $journey->id)->count());
    }

    public function test_routes_to_correct_product_when_product_interest_answer_is_provided(): void
    {
        $payload = $this->qualifyPayload();
        $payload['answers'][] = ['step_key' => 'step_2', 'field_key' => 'product_interest', 'value' => 'zoho_marketing_plus'];

        $this->postJson('/api/funnel/qualify', $payload)
            ->assertCreated()
            ->assertJsonPath('product_key', 'zoho_marketing_plus');
    }

    public function test_stores_utm_and_referrer_on_the_journey(): void
    {
        $payload = array_merge($this->qualifyPayload(), [
            'utm_source' => 'google',
            'utm_medium' => 'cpc',
            'utm_campaign' => 'zoho-test',
            'referrer' => 'https://google.com',
        ]);

        $response = $this->postJson('/api/funnel/qualify', $payload);
        $response->assertCreated();

        /** @var string $token */
        $token = $response->json('journey_token');
        $journey = Journey::where('journey_token', $token)->firstOrFail();

        $this->assertSame('google', $journey->utm_source);
        $this->assertSame('cpc', $journey->utm_medium);
        $this->assertSame('zoho-test', $journey->utm_campaign);
        $this->assertSame('https://google.com', $journey->referrer);
    }

    public function test_rejects_qualification_without_consent(): void
    {
        $payload = $this->qualifyPayload();
        $payload['consent'] = false;

        $this->postJson('/api/funnel/qualify', $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['consent']);
    }

    public function test_rejects_an_inactive_landing_page(): void
    {
        Page::factory()->create([
            'page_key' => 'landing_inactive',
            'page_type' => PageType::Landing,
            'slug' => '/landing-inactive',
            'is_active' => false,
        ]);

        $this->postJson('/api/funnel/qualify', $this->qualifyPayload('landing_inactive'))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['source_page_key']);
    }

    public function test_rejects_a_non_landing_page_as_source(): void
    {
        Page::factory()->create([
            'page_key' => 'zoho_one',
            'page_type' => PageType::Product,
            'slug' => '/products/zoho-one',
            'is_active' => true,
        ]);

        $this->postJson('/api/funnel/qualify', $this->qualifyPayload('zoho_one'))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['source_page_key']);
    }

    public function test_rejects_a_non_existent_source_page(): void
    {
        $this->postJson('/api/funnel/qualify', $this->qualifyPayload('does_not_exist'))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['source_page_key']);
    }

    public function test_rejects_missing_answers(): void
    {
        $payload = $this->qualifyPayload();
        $payload['answers'] = [];

        $this->postJson('/api/funnel/qualify', $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['answers']);
    }
}
