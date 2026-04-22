<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\SyncAttemptStatus;
use App\Models\CrmSyncAttempt;
use App\Models\Submission;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CrmSyncAttempt>
 */
class CrmSyncAttemptFactory extends Factory
{
    public function definition(): array
    {
        return [
            'submission_id' => Submission::factory(),
            'provider' => 'zoho_crm',
            'action' => 'create_lead',
            'request_payload' => [],
            'response_payload' => null,
            'status' => SyncAttemptStatus::Pending,
            'error_code' => null,
            'error_message' => null,
            'attempted_at' => now(),
        ];
    }

    public function success(): static
    {
        return $this->state([
            'status' => SyncAttemptStatus::Success,
            'response_payload' => ['id' => fake()->numerify('########')],
        ]);
    }

    public function failed(string $code = 'API_ERROR', string $message = 'Request failed'): static
    {
        return $this->state([
            'status' => SyncAttemptStatus::Failed,
            'error_code' => $code,
            'error_message' => $message,
        ]);
    }
}
