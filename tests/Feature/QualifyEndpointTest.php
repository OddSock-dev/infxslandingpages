<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JourneyStatus;
use App\Models\Journey;
use App\Models\JourneyAnswer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Component;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Tests\TestCase;

class QualifyEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_journey_and_answer_records_from_the_public_qualifier_component(): void
    {
        /** @var Testable<Component> $component */
        $component = Livewire::test('marketing.qualifier');

        $component
            ->set('primaryGoal', 'unify_operations')
            ->set('biggestGap', 'manual_handoffs')
            ->set('teamShape', 'multi_department_growth')
            ->set('timeline', 'now')
            ->set('name', 'Jane Doe')
            ->set('email', 'jane@example.com')
            ->set('consent', true)
            ->call('qualify')
            ->assertRedirect();

        $journey = Journey::query()->firstOrFail();
        $answer = JourneyAnswer::query()->where('field_key', 'primary_goal')->firstOrFail();

        $this->assertSame(JourneyStatus::Routed, $journey->status);
        $this->assertSame('landing', $journey->source_page_key);
        $this->assertSame('zoho_one', $journey->assigned_product_key);
        $this->assertNotNull($journey->consent_at);
        $this->assertSame($journey->id, $answer->journey_id);
        $this->assertSame('primary_goal', $answer->field_key);
        $this->assertSame('unify_operations', $answer->value);
        $this->assertIsArray($journey->contact_json);
        $this->assertSame('Jane Doe', $journey->contact_json['name'] ?? null);
    }

    public function test_routes_to_the_selected_product_when_qualifying(): void
    {
        /** @var Testable<Component> $component */
        $component = Livewire::test('marketing.qualifier');

        $component
            ->set('primaryGoal', 'measure_marketing')
            ->set('biggestGap', 'campaign_visibility')
            ->set('teamShape', 'marketing_led')
            ->set('timeline', 'this_quarter')
            ->set('name', 'Jane Doe')
            ->set('email', 'jane@example.com')
            ->set('consent', true)
            ->call('qualify')
            ->assertRedirect();

        $journey = Journey::query()->firstOrFail();

        $this->assertSame('zoho_marketing_plus', $journey->assigned_product_key);
    }

    public function test_stores_utm_context_on_the_journey(): void
    {
        /** @var Testable<Component> $component */
        $component = Livewire::withQueryParams([
            'utm_source' => 'google',
            'utm_medium' => 'cpc',
            'utm_campaign' => 'zoho-test',
            'utm_term' => 'crm',
            'utm_content' => 'hero-button',
            // @phpstan-ignore-next-line File-based Livewire aliases do not expose a resolvable template type here.
        ])->test('marketing.qualifier');

        $component
            ->set('primaryGoal', 'modernise_collaboration')
            ->set('biggestGap', 'email_docs_chat')
            ->set('teamShape', 'distributed_team')
            ->set('timeline', 'this_year')
            ->set('name', 'Jane Doe')
            ->set('email', 'jane@example.com')
            ->set('consent', true)
            ->call('qualify')
            ->assertRedirect();

        $journey = Journey::query()->firstOrFail();

        $this->assertSame('google', $journey->utm_source);
        $this->assertSame('cpc', $journey->utm_medium);
        $this->assertSame('zoho-test', $journey->utm_campaign);
        $this->assertSame('crm', $journey->utm_term);
        $this->assertSame('hero-button', $journey->utm_content);
    }

    public function test_requires_a_selected_product_before_advancing(): void
    {
        /** @var Testable<Component> $component */
        $component = Livewire::test('marketing.qualifier');

        $component
            ->call('nextStep')
            ->assertHasErrors(['primaryGoal']);
    }

    public function test_requires_consent_before_qualifying(): void
    {
        /** @var Testable<Component> $component */
        $component = Livewire::test('marketing.qualifier');

        $component
            ->set('primaryGoal', 'unify_operations')
            ->set('biggestGap', 'manual_handoffs')
            ->set('teamShape', 'multi_department_growth')
            ->set('timeline', 'now')
            ->set('name', 'Jane Doe')
            ->set('email', 'jane@example.com')
            ->call('qualify')
            ->assertHasErrors(['consent']);
    }
}
