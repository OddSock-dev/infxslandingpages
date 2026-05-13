<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Journey;
use App\Models\TrialClick;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<TrialClick>
 */
class TrialClickFactory extends Factory
{
    public function definition(): array
    {
        return [
            'journey_id' => null,
            'session_id' => (string) Str::uuid(),
            'page_key' => 'zoho_one',
            'product_key' => 'zoho_one',
            'source_page_key' => 'zoho_one',
            'target_url' => 'https://store.zoho.com/ResellerCustomerSignUp.do?id=b90ffafff590634f12c003a7325340d7',
            'click_fingerprint' => hash('sha256', (string) Str::ulid()),
            'meta_json' => [],
            'clicked_at' => now(),
        ];
    }

    public function forJourney(Journey $journey): static
    {
        return $this->state([
            'journey_id' => $journey->id,
            'page_key' => $journey->assigned_product_key ?? 'zoho_one',
            'product_key' => $journey->assigned_product_key ?? 'zoho_one',
            'source_page_key' => $journey->source_page_key,
        ]);
    }
}
