<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\JourneyStatus;
use App\Models\Journey;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Journey>
 */
class JourneyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'journey_token' => (string) Str::ulid(),
            'source_page_key' => 'landing',
            'assigned_product_key' => null,
            'status' => JourneyStatus::Qualifying,
            'utm_source' => null,
            'utm_medium' => null,
            'utm_campaign' => null,
            'utm_term' => null,
            'utm_content' => null,
            'referrer' => null,
            'ip_hash' => null,
            'user_agent' => fake()->userAgent(),
            'consent_at' => null,
            'expires_at' => now()->addHours(24),
        ];
    }

    public function routed(string $productKey = 'zoho_marketing_plus'): static
    {
        return $this->state([
            'status' => JourneyStatus::Routed,
            'assigned_product_key' => $productKey,
        ]);
    }

    public function submitted(): static
    {
        return $this->state(['status' => JourneyStatus::Submitted]);
    }

    public function expired(): static
    {
        return $this->state([
            'status' => JourneyStatus::Expired,
            'expires_at' => now()->subHour(),
        ]);
    }
}
