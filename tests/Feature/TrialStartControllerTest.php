<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Journey;
use App\Models\TrialClick;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class TrialStartControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_records_a_signed_trial_start_and_redirects_to_the_trial_url(): void
    {
        $journey = Journey::factory()->routed('zoho_one')->create([
            'source_page_key' => 'landing',
            'utm_source' => 'google',
            'utm_campaign' => 'demand_sprint',
            'referrer' => 'https://www.google.com/search?q=zoho',
        ]);

        $url = URL::temporarySignedRoute('products.trial.start', now()->addDay(), [
            'pageKey' => 'zoho_one',
            'journey' => $journey->journey_token,
        ]);

        $this->get($url)
            ->assertRedirect('https://store.zoho.com/ResellerCustomerSignUp.do?id=b90ffafff590634f12c003a7325340d7');

        $trialClick = TrialClick::query()->firstOrFail();

        $this->assertSame($journey->id, $trialClick->journey_id);
        $this->assertSame('zoho_one', $trialClick->page_key);
        $this->assertSame('landing', $trialClick->source_page_key);
        $this->assertSame('google', $trialClick->meta_json['utm_source']);
        $this->assertSame('demand_sprint', $trialClick->meta_json['utm_campaign']);
        $this->assertSame('https://www.google.com/search?q=zoho', $trialClick->meta_json['journey_referrer']);
    }

    public function test_deduplicates_trial_starts_for_the_same_signed_link_and_session(): void
    {
        $journey = Journey::factory()->routed('zoho_one')->create();

        $url = URL::temporarySignedRoute('products.trial.start', now()->addDay(), [
            'pageKey' => 'zoho_one',
            'journey' => $journey->journey_token,
        ]);

        $this->get($url)->assertRedirect();
        $this->get($url)->assertRedirect();

        $this->assertDatabaseCount('trial_clicks', 1);
    }

    public function test_rejects_unsigned_trial_start_links(): void
    {
        $this->get(route('products.trial.start', ['pageKey' => 'zoho_one']))
            ->assertForbidden();
    }
}
