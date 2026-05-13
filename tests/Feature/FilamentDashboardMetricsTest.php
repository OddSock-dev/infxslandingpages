<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\CrmStatus;
use App\Filament\Widgets\AttributionInsightsWidget;
use App\Filament\Widgets\FunnelOverviewWidget;
use App\Filament\Widgets\FunnelPagePerformanceWidget;
use App\Filament\Widgets\LeadQualitySegmentationWidget;
use App\Filament\Widgets\QualificationDropOffWidget;
use App\Filament\Widgets\ZohoConnectionHealthWidget;
use App\Models\CrmSyncAttempt;
use App\Models\Journey;
use App\Models\Submission;
use App\Models\TrialClick;
use App\Models\User;
use App\Models\ZohoCredential;
use App\Services\DashboardMetricsService;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FilamentDashboardMetricsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->actingAs($this->admin);
    }

    public function test_dashboard_metrics_service_aggregates_funnel_and_pipeline_data(): void
    {
        $openJourney = Journey::factory()->routed('zoho_one')->create([
            'source_page_key' => 'landing',
            'created_at' => CarbonImmutable::now()->subDays(10),
            'expires_at' => CarbonImmutable::now()->addDay(),
            'utm_source' => 'google',
            'utm_campaign' => 'demand_sprint',
        ]);

        $stalledJourney = Journey::factory()->routed('zoho_marketing_plus')->create([
            'source_page_key' => 'landing',
            'created_at' => CarbonImmutable::now()->subDays(9),
            'expires_at' => CarbonImmutable::now()->subHour(),
            'utm_source' => 'linkedin',
            'utm_campaign' => 'pipeline_push',
        ]);

        $submittedJourneyOne = Journey::factory()->submitted()->create([
            'source_page_key' => 'landing',
            'assigned_product_key' => 'zoho_marketing_plus',
            'created_at' => CarbonImmutable::now()->subDays(8),
            'expires_at' => CarbonImmutable::now()->addDay(),
            'utm_source' => 'google',
            'utm_campaign' => 'demand_sprint',
        ]);

        $submittedJourneyTwo = Journey::factory()->submitted()->create([
            'source_page_key' => 'landing',
            'assigned_product_key' => 'zoho_workplace',
            'created_at' => CarbonImmutable::now()->subDays(7),
            'expires_at' => CarbonImmutable::now()->addDay(),
            'utm_source' => 'linkedin',
            'utm_campaign' => 'workspace_refresh',
        ]);

        Journey::factory()->create([
            'source_page_key' => 'landing',
            'created_at' => CarbonImmutable::now()->subDays(6),
            'expires_at' => CarbonImmutable::now()->addDay(),
        ]);

        $journeySubmissionOne = Submission::factory()->synced()->create([
            'journey_id' => $submittedJourneyOne->id,
            'page_key' => 'zoho_marketing_plus',
            'product_key' => 'zoho_marketing_plus',
            'crm_status' => CrmStatus::Synced,
            'submitted_at' => CarbonImmutable::now()->subDays(8),
            'meta_json' => [
                'utm_source' => 'google',
                'utm_campaign' => 'demand_sprint',
                'source_page_key' => 'landing',
                'company_size_band' => '51_200',
                'implementation_timeline' => 'now',
            ],
        ]);

        $journeySubmissionTwo = Submission::factory()->failed()->create([
            'journey_id' => $submittedJourneyTwo->id,
            'page_key' => 'zoho_workplace',
            'product_key' => 'zoho_workplace',
            'crm_status' => CrmStatus::Failed,
            'submitted_at' => CarbonImmutable::now()->subDays(7),
            'meta_json' => [
                'utm_source' => 'linkedin',
                'utm_campaign' => 'workspace_refresh',
                'source_page_key' => 'landing',
                'company_size_band' => '1_10',
                'implementation_timeline' => 'exploring',
            ],
        ]);

        $directHighIntentSubmission = Submission::factory()->synced()->create([
            'journey_id' => null,
            'page_key' => 'zoho_marketing_plus',
            'product_key' => 'zoho_marketing_plus',
            'crm_status' => CrmStatus::Synced,
            'submitted_at' => CarbonImmutable::now()->subDays(2),
            'meta_json' => [
                'utm_source' => 'google',
                'utm_campaign' => 'demand_sprint',
                'company_size_band' => '11_50',
                'implementation_timeline' => 'this_quarter',
            ],
        ]);

        $directWarmSubmission = Submission::factory()->create([
            'journey_id' => null,
            'page_key' => 'zoho_one',
            'product_key' => 'zoho_one',
            'crm_status' => CrmStatus::Pending,
            'submitted_at' => CarbonImmutable::now()->subDay(),
            'meta_json' => [
                'company_size_band' => '1_10',
                'implementation_timeline' => 'this_year',
            ],
        ]);

        CrmSyncAttempt::factory()->success()->create([
            'submission_id' => $journeySubmissionOne->id,
            'attempted_at' => CarbonImmutable::now()->subDays(2),
        ]);

        CrmSyncAttempt::factory()->failed()->create([
            'submission_id' => $journeySubmissionTwo->id,
            'attempted_at' => CarbonImmutable::now()->subHours(20),
        ]);

        CrmSyncAttempt::factory()->success()->create([
            'submission_id' => $directHighIntentSubmission->id,
            'attempted_at' => CarbonImmutable::now()->subHour(),
        ]);

        TrialClick::factory()->forJourney($submittedJourneyOne)->create([
            'page_key' => 'zoho_marketing_plus',
            'product_key' => 'zoho_marketing_plus',
            'source_page_key' => 'landing',
            'clicked_at' => CarbonImmutable::now()->subDays(8),
        ]);

        TrialClick::factory()->forJourney($submittedJourneyTwo)->create([
            'page_key' => 'zoho_workplace',
            'product_key' => 'zoho_workplace',
            'source_page_key' => 'landing',
            'clicked_at' => CarbonImmutable::now()->subDays(7),
        ]);

        TrialClick::factory()->create([
            'page_key' => 'zoho_one',
            'product_key' => 'zoho_one',
            'source_page_key' => 'zoho_one',
            'clicked_at' => CarbonImmutable::now()->subDay(),
        ]);

        ZohoCredential::set(ZohoCredential::REFRESH_TOKEN, 'refresh-token');
        ZohoCredential::set(ZohoCredential::ACCESS_TOKEN, 'access-token', CarbonImmutable::now()->addHour());

        $metrics = app(DashboardMetricsService::class);

        $funnel = $metrics->funnelOverview();
        $this->assertSame(5, $funnel['journeys_started']);
        $this->assertSame(4, $funnel['recommendations_issued']);
        $this->assertSame(2, $funnel['journey_consultations']);
        $this->assertSame(3, $funnel['trial_starts']);
        $this->assertSame(1, $funnel['stalled_handoffs']);

        $pageConversion = $metrics->pageConversion();
        $this->assertSame(['Homepage', 'Zoho One', 'Zoho Marketing Plus'], $pageConversion['labels']);
        $this->assertSame([5, 0, 0], $pageConversion['journey_starts']);
        $this->assertSame([2, 1, 1], $pageConversion['consultations']);
        $this->assertSame([2, 1, 0], $pageConversion['trial_starts']);

        $dropOff = $metrics->qualificationDropOff();
        $this->assertSame(['Zoho One', 'Zoho Marketing Plus', 'Zoho Workplace'], $dropOff['labels']);
        $this->assertSame([1, 0, 0], $dropOff['open']);
        $this->assertSame([0, 1, 0], $dropOff['stalled']);
        $this->assertSame([0, 1, 1], $dropOff['submitted']);

        $attribution = $metrics->attributionOverview();
        $this->assertSame(3, $attribution['attributed_leads']);
        $this->assertSame(1, $attribution['unattributed_leads']);
        $this->assertSame('Google', $attribution['top_source']);
        $this->assertSame('Demand Sprint', $attribution['top_campaign']);
        $this->assertSame('Homepage', $attribution['top_entry_page']);

        $leadQuality = $metrics->leadQualityOverview();
        $this->assertSame(2, $leadQuality['high_intent']);
        $this->assertSame(1, $leadQuality['warm_pipeline']);
        $this->assertSame(1, $leadQuality['nurture_needed']);
        $this->assertSame('Zoho Marketing Plus', $leadQuality['top_high_intent_product']);
        $this->assertSame(2, $leadQuality['top_high_intent_product_count']);

        $sync = $metrics->syncHealth();
        $this->assertSame(1, $sync['failed_last_day']);
        $this->assertSame(1, $sync['pending_submissions']);
        $this->assertSame(66.7, $sync['success_rate_last_7d']);
        $this->assertNotNull($sync['last_success_at']);

        $this->assertTrue($openJourney->exists);
        $this->assertTrue($stalledJourney->exists);
        $this->assertTrue($directWarmSubmission->exists);
    }

    public function test_dashboard_metric_widgets_render_for_admins(): void
    {
        ZohoCredential::set(ZohoCredential::REFRESH_TOKEN, 'refresh-token');
        ZohoCredential::set(ZohoCredential::ACCESS_TOKEN, 'access-token', CarbonImmutable::now()->addHour());

        Livewire::test(FunnelOverviewWidget::class)->assertOk();
        Livewire::test(FunnelPagePerformanceWidget::class)->assertOk();
        Livewire::test(QualificationDropOffWidget::class)->assertOk();
        Livewire::test(AttributionInsightsWidget::class)->assertOk();
        Livewire::test(LeadQualitySegmentationWidget::class)->assertOk();
        Livewire::test(ZohoConnectionHealthWidget::class)->assertOk();
    }
}
