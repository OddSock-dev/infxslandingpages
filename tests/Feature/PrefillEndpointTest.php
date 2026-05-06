<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Journey;
use App\Models\JourneyAnswer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Component;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Tests\TestCase;

class PrefillEndpointTest extends TestCase
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

    /**
     * @return Testable<Component>
     */
    private function productLeadComponentForToken(string $token, string $pageKey = 'zoho_one', string $productName = 'Zoho One'): Testable
    {
        $livewire = Livewire::withQueryParams(['t' => $token]);

        /** @var Testable<Component> $component */
        // @phpstan-ignore-next-line File-based Livewire components are resolved via runtime aliases.
        $component = $livewire->test('marketing.product-lead', [
            'pageKey' => $pageKey,
            'productName' => $productName,
            'trialUrl' => $this->trialUrlFor($pageKey),
        ]);

        return $component;
    }

    public function test_surfaces_safe_context_for_a_valid_journey_token(): void
    {
        $journey = Journey::factory()->routed('zoho_one')->create();

        JourneyAnswer::factory()->create([
            'journey_id' => $journey->id,
            'step_key' => 'diagnostic_1',
            'field_key' => 'primary_goal',
            'value' => 'unify_operations',
        ]);

        JourneyAnswer::factory()->create([
            'journey_id' => $journey->id,
            'step_key' => 'diagnostic_2',
            'field_key' => 'biggest_gap',
            'value' => 'manual_handoffs',
        ]);

        JourneyAnswer::factory()->create([
            'journey_id' => $journey->id,
            'step_key' => 'diagnostic_3',
            'field_key' => 'team_shape',
            'value' => 'multi_department_growth',
        ]);

        JourneyAnswer::factory()->create([
            'journey_id' => $journey->id,
            'step_key' => 'diagnostic_4',
            'field_key' => 'timeline',
            'value' => 'this_quarter',
        ]);

        JourneyAnswer::factory()->create([
            'journey_id' => $journey->id,
            'step_key' => 'step_1',
            'field_key' => 'email',
            'value' => 'secret@example.com',
        ]);

        /** @var Testable<Component> $component */
        $component = $this->productLeadComponentForToken($journey->journey_token);

        $component->assertSee('Based on what you shared, Zoho One looks like a strong fit for your business. Here is why:');
        $component->assertSee('You want your teams working from one connected system instead of juggling disconnected tools.');
        $component->assertSee('Too much manual work is slowing the business down, so a more connected platform would save time and reduce duplication.');
        $component->assertSee('Several departments need to work from the same reliable information as the business grows.');
        $component->assertSee('Your Earlier Answers');
        $component->assertSee('Unify Operations');
        $component->assertDontSee('secret@example.com');
    }

    public function test_hides_captured_context_when_no_safe_answers_exist(): void
    {
        $journey = Journey::factory()->routed('zoho_workplace')->create();

        /** @var Testable<Component> $component */
        $component = $this->productLeadComponentForToken($journey->journey_token, 'zoho_workplace', 'Zoho Workplace');

        $component->assertSee('get your next step ready');
        $component->assertDontSee('Your Earlier Answers');
        $component->assertSee('Start Free Trial');
    }

    public function test_expired_journeys_fall_back_to_a_fresh_request(): void
    {
        $journey = Journey::factory()->expired()->create();

        /** @var Testable<Component> $component */
        $component = $this->productLeadComponentForToken($journey->journey_token);

        $component->assertSee('start fresh');
        $component->assertDontSee('Your Earlier Answers');
    }

    public function test_unknown_tokens_fall_back_to_a_fresh_request(): void
    {
        /** @var Testable<Component> $component */
        $component = $this->productLeadComponentForToken('01JZZZZZZZZZZZZZZZZZZZZZZ');

        $component->assertSee('start fresh');
        $component->assertDontSee('Your Earlier Answers');
    }
}
