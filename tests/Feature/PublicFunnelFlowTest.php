<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JourneyStatus;
use App\Jobs\SyncSubmissionToZohoJob;
use App\Models\Journey;
use App\Models\Submission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Livewire\Component;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Tests\TestCase;

class PublicFunnelFlowTest extends TestCase
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

    public function test_creates_a_routed_journey_from_the_public_qualifier_component(): void
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

        $this->assertSame(JourneyStatus::Routed, $journey->status);
        $this->assertSame('zoho_marketing_plus', $journey->assigned_product_key);
    }

    public function test_submits_a_consultation_request_from_the_public_product_lead_component(): void
    {
        Queue::fake();

        $journey = Journey::factory()->routed('zoho_one')->create();

        $livewire = Livewire::withQueryParams(['t' => $journey->journey_token]);

        /** @var Testable<Component> $component */
        // @phpstan-ignore-next-line File-based Livewire components are resolved via runtime aliases.
        $component = $livewire->test('marketing.product-lead', [
            'pageKey' => 'zoho_one',
            'productName' => 'Zoho One',
            'trialUrl' => $this->trialUrlFor('zoho_one'),
        ]);

        $component
            ->call('requestConsultation')
            ->set('companySizeBand', '11_50')
            ->set('implementationTimeline', 'this_quarter')
            ->set('currentEnvironment', 'multiple_line_of_business_tools')
            ->set('priorityOutcome', 'shared_operating_system')
            ->set('name', 'Jane Doe')
            ->set('email', 'jane@example.com')
            ->set('phone', '+27211234567')
            ->set('company', 'ACME Ltd')
            ->call('submit')
            ->assertRedirect();

        $submission = Submission::query()->firstOrFail();

        $this->assertSame($journey->id, $submission->journey_id);
        $this->assertSame('zoho_one', $submission->product_key);

        Queue::assertPushed(SyncSubmissionToZohoJob::class);
    }
}
