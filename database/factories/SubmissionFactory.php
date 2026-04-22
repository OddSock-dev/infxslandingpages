<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\CrmStatus;
use App\Models\Submission;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Submission>
 */
class SubmissionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'journey_id' => null,
            'page_key' => 'zoho_marketing_plus',
            'product_key' => 'zoho_marketing_plus',
            'pii_json' => [
                'first_name' => fake()->firstName(),
                'last_name' => fake()->lastName(),
                'email' => fake()->safeEmail(),
                'phone' => fake()->phoneNumber(),
                'company' => fake()->company(),
            ],
            'meta_json' => [],
            'crm_status' => CrmStatus::Pending,
            'submitted_at' => now(),
        ];
    }

    public function synced(): static
    {
        return $this->state(['crm_status' => CrmStatus::Synced]);
    }

    public function failed(): static
    {
        return $this->state(['crm_status' => CrmStatus::Failed]);
    }
}
