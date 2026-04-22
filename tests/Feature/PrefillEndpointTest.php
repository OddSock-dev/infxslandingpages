<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Journey;
use App\Models\JourneyAnswer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrefillEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_product_key_and_safe_fields_for_a_valid_token(): void
    {
        $journey = Journey::factory()->routed('zoho_one')->create();

        JourneyAnswer::factory()->create([
            'journey_id' => $journey->id,
            'step_key' => 'step_1',
            'field_key' => 'business_size',
            'value' => '11-50',
        ]);

        JourneyAnswer::factory()->create([
            'journey_id' => $journey->id,
            'step_key' => 'step_1',
            'field_key' => 'email',
            'value' => 'secret@example.com',
        ]);

        $response = $this->getJson("/api/journeys/{$journey->journey_token}/prefill");

        $response->assertOk()
            ->assertJsonPath('product_key', 'zoho_one')
            ->assertJsonPath('fields.business_size', '11-50');

        $response->assertJsonMissing(['email' => 'secret@example.com']);

        /** @var array<string, string> $fields */
        $fields = $response->json('fields');
        $this->assertArrayNotHasKey('email', $fields);
    }

    public function test_returns_empty_fields_array_when_no_answers_exist(): void
    {
        $journey = Journey::factory()->routed('zoho_workplace')->create();

        $this->getJson("/api/journeys/{$journey->journey_token}/prefill")
            ->assertOk()
            ->assertJsonPath('product_key', 'zoho_workplace')
            ->assertJsonPath('fields', []);
    }

    public function test_returns_404_for_an_expired_journey(): void
    {
        $journey = Journey::factory()->expired()->create();

        $this->getJson("/api/journeys/{$journey->journey_token}/prefill")
            ->assertNotFound();
    }

    public function test_returns_404_for_an_unknown_token(): void
    {
        $this->getJson('/api/journeys/01JZZZZZZZZZZZZZZZZZZZZZZ/prefill')
            ->assertNotFound();
    }
}
