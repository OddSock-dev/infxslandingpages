<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\CrmStatus;
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

class SubmitEndpointTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    /**
     * @return array<string, string>
     */
    private function baseSubmissionPayload(): array
    {
        return [
            'companySizeBand' => '11_50',
            'implementationTimeline' => 'this_quarter',
            'currentEnvironment' => 'multiple_line_of_business_tools',
            'priorityOutcome' => 'shared_operating_system',
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '+27211234567',
            'company' => 'ACME Ltd',
        ];
    }

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
    private function productLeadComponent(?string $journeyToken = null, string $pageKey = 'zoho_one', string $productName = 'Zoho One'): Testable
    {
        if ($journeyToken === null) {
            /** @var Testable<Component> $component */
            $component = Livewire::test('marketing.product-lead', [
                'pageKey' => $pageKey,
                'productName' => $productName,
                'trialUrl' => $this->trialUrlFor($pageKey),
            ]);

            return $component;
        }

        $livewire = Livewire::withQueryParams(['t' => $journeyToken]);

        /** @var Testable<Component> $component */
        // @phpstan-ignore-next-line File-based Livewire components are resolved via runtime aliases.
        $component = $livewire->test('marketing.product-lead', [
            'pageKey' => $pageKey,
            'productName' => $productName,
            'trialUrl' => $this->trialUrlFor($pageKey),
        ]);

        return $component;
    }

    public function test_creates_a_submission_without_a_journey_token(): void
    {
        $this->productLeadComponent()
            ->set('companySizeBand', $this->baseSubmissionPayload()['companySizeBand'])
            ->set('implementationTimeline', $this->baseSubmissionPayload()['implementationTimeline'])
            ->set('currentEnvironment', $this->baseSubmissionPayload()['currentEnvironment'])
            ->set('priorityOutcome', $this->baseSubmissionPayload()['priorityOutcome'])
            ->call('completeQualification')
            ->call('requestConsultation')
            ->set('name', $this->baseSubmissionPayload()['name'])
            ->set('email', $this->baseSubmissionPayload()['email'])
            ->set('phone', $this->baseSubmissionPayload()['phone'])
            ->set('company', $this->baseSubmissionPayload()['company'])
            ->call('submit')
            ->assertRedirect(route('thanks'));

        $submission = Submission::query()->firstOrFail();

        $this->assertSame('zoho_one', $submission->product_key);
        $this->assertSame(CrmStatus::Pending, $submission->crm_status);
        $this->assertNull($submission->journey_id);

        Queue::assertPushed(SyncSubmissionToZohoJob::class, fn (SyncSubmissionToZohoJob $job): bool => $job->submissionId === $submission->id);
    }

    public function test_creates_a_submission_linked_to_a_valid_journey_token(): void
    {
        $journey = Journey::factory()->routed('zoho_one')->create();

        $this->productLeadComponent($journey->journey_token)
            ->call('requestConsultation')
            ->set('companySizeBand', $this->baseSubmissionPayload()['companySizeBand'])
            ->set('implementationTimeline', $this->baseSubmissionPayload()['implementationTimeline'])
            ->set('currentEnvironment', $this->baseSubmissionPayload()['currentEnvironment'])
            ->set('priorityOutcome', $this->baseSubmissionPayload()['priorityOutcome'])
            ->set('name', $this->baseSubmissionPayload()['name'])
            ->set('email', $this->baseSubmissionPayload()['email'])
            ->set('phone', $this->baseSubmissionPayload()['phone'])
            ->set('company', $this->baseSubmissionPayload()['company'])
            ->call('submit')
            ->assertRedirect(route('thanks'));

        $submission = Submission::query()->firstOrFail();
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

        $this->productLeadComponent($journey->journey_token)
            ->call('requestConsultation')
            ->set('companySizeBand', '11_50')
            ->set('implementationTimeline', 'this_quarter')
            ->set('currentEnvironment', 'multiple_line_of_business_tools')
            ->set('priorityOutcome', 'shared_operating_system')
            ->set('name', 'Jane Doe')
            ->set('email', 'jane@example.com')
            ->call('submit')
            ->assertRedirect(route('thanks'));

        $meta = Submission::query()->firstOrFail()->meta_json;

        $this->assertSame('facebook', $meta['utm_source']);
        $this->assertSame('q1-promo', $meta['utm_campaign']);
    }

    public function test_starts_a_fresh_submission_when_the_journey_token_is_expired(): void
    {
        $journey = Journey::factory()->expired()->create();

        /** @var Testable<Component> $component */
        $component = $this->productLeadComponent($journey->journey_token);

        $component->assertSee('start fresh');

        $component
            ->set('companySizeBand', '11_50')
            ->set('implementationTimeline', 'this_quarter')
            ->set('currentEnvironment', 'multiple_line_of_business_tools')
            ->set('priorityOutcome', 'shared_operating_system')
            ->call('completeQualification')
            ->call('requestConsultation')
            ->set('name', 'Jane Doe')
            ->set('email', 'jane@example.com')
            ->call('submit')
            ->assertRedirect(route('thanks'));

        $submission = Submission::query()->firstOrFail();
        $this->assertNull($submission->journey_id);
    }

    public function test_blocks_resubmission_for_an_already_submitted_journey(): void
    {
        $journey = Journey::factory()->routed('zoho_one')->submitted()->create();

        /** @var Testable<Component> $component */
        $component = $this->productLeadComponent($journey->journey_token);

        $component
            ->call('requestConsultation')
            ->set('companySizeBand', '11_50')
            ->set('implementationTimeline', 'this_quarter')
            ->set('currentEnvironment', 'manual_ops_and_reporting')
            ->set('priorityOutcome', 'privacy_and_control')
            ->set('name', 'Jane Doe')
            ->set('email', 'jane@example.com')
            ->call('submit')
            ->assertSee('This request has already been submitted.');

        $this->assertDatabaseCount('submissions', 0);
    }

    public function test_ignores_a_mismatched_journey_token_and_submits_a_fresh_request(): void
    {
        $journey = Journey::factory()->routed('zoho_one')->create();

        /** @var Testable<Component> $component */
        $component = $this->productLeadComponent($journey->journey_token, 'zoho_workplace', 'Zoho Workplace');

        $component->assertSee('start fresh');

        $component
            ->set('companySizeBand', '11_50')
            ->set('implementationTimeline', 'this_quarter')
            ->set('currentEnvironment', 'google_microsoft_mix')
            ->set('priorityOutcome', 'cleaner_collaboration')
            ->call('completeQualification')
            ->call('requestConsultation')
            ->set('name', 'Jane Doe')
            ->set('email', 'jane@example.com')
            ->call('submit')
            ->assertRedirect(route('thanks'));

        $submission = Submission::query()->firstOrFail();
        $this->assertNull($submission->journey_id);
        $this->assertSame('zoho_workplace', $submission->product_key);
    }

    public function test_blocks_submitting_before_the_quick_fit_is_complete(): void
    {
        $this->productLeadComponent()
            ->call('submit')
            ->assertSee('Answer the four quick questions before choosing your next step.');
    }

    public function test_validates_required_contact_fields_after_the_next_step_is_unlocked(): void
    {
        $this->productLeadComponent()
            ->set('companySizeBand', '11_50')
            ->set('implementationTimeline', 'this_quarter')
            ->set('currentEnvironment', 'multiple_line_of_business_tools')
            ->set('priorityOutcome', 'shared_operating_system')
            ->call('completeQualification')
            ->call('requestConsultation')
            ->call('submit')
            ->assertHasErrors(['name', 'email']);
    }

    public function test_rejects_an_invalid_email_address(): void
    {
        $this->productLeadComponent()
            ->set('companySizeBand', '11_50')
            ->set('implementationTimeline', 'this_quarter')
            ->set('currentEnvironment', 'multiple_line_of_business_tools')
            ->set('priorityOutcome', 'shared_operating_system')
            ->call('completeQualification')
            ->call('requestConsultation')
            ->set('name', 'Jane Doe')
            ->set('email', 'not-an-email')
            ->call('submit')
            ->assertHasErrors(['email']);
    }
}
