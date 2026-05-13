<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\SyncAttemptStatus;
use App\Filament\Resources\CrmSyncAttempts\Pages\ViewCrmSyncAttempt;
use App\Filament\Resources\Submissions\Pages\ListSubmissions;
use App\Filament\Resources\Submissions\Pages\ViewSubmission;
use App\Models\CrmSyncAttempt;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Component;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Tests\TestCase;

class FilamentPiiVisibilityTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();

        $this->actingAs($this->admin);
    }

    public function test_submission_list_shows_unredacted_email_to_admins(): void
    {
        Submission::factory()->create([
            'pii_json' => [
                'name' => 'Jane Doe',
                'email' => 'jane@example.com',
                'phone' => '+27211234567',
                'company' => 'Acme Pty Ltd',
            ],
        ]);

        /** @var Testable<Component> $component */
        $component = Livewire::test(ListSubmissions::class);

        $component->assertSee('jane@example.com');
    }

    public function test_submission_view_shows_unredacted_contact_and_error_details_to_admins(): void
    {
        $attempt = $this->failedAttempt();

        /** @var Testable<Component> $component */
        $component = Livewire::test(ViewSubmission::class, ['record' => $attempt->submission_id]);

        $component->assertSee('Jane Doe');
        $component->assertSee('jane@example.com');
        $component->assertSee('+27211234567');
        $component->assertSee('Acme Pty Ltd');
        $component->assertSee('Lead jane@example.com with phone +27211234567 was rejected.');
    }

    public function test_crm_sync_attempt_view_shows_unredacted_payloads_to_admins(): void
    {
        $attempt = $this->failedAttempt();

        /** @var Testable<Component> $component */
        $component = Livewire::test(ViewCrmSyncAttempt::class, ['record' => $attempt->id]);

        $component->assertSee('jane@example.com');
        $component->assertSee('Acme Pty Ltd');
        $component->assertSee('Lead jane@example.com with phone +27211234567 was rejected.');
        $component->assertSee('response@example.com');
    }

    private function failedAttempt(): CrmSyncAttempt
    {
        $submission = Submission::factory()->failed()->create([
            'pii_json' => [
                'name' => 'Jane Doe',
                'email' => 'jane@example.com',
                'phone' => '+27211234567',
                'company' => 'Acme Pty Ltd',
            ],
        ]);

        return CrmSyncAttempt::factory()
            ->for($submission)
            ->state([
                'request_payload' => [
                    'data' => [[
                        'Email' => 'jane@example.com',
                        'Company' => 'Acme Pty Ltd',
                    ]],
                ],
                'response_payload' => [
                    'data' => [[
                        'details' => [
                            'api_name' => 'Email',
                            'json_path' => '$.data[0].Email',
                            'provided' => 'response@example.com',
                        ],
                    ]],
                ],
                'status' => SyncAttemptStatus::Failed,
                'error_code' => 'HTTP_422_VALIDATION',
                'error_message' => 'Lead jane@example.com with phone +27211234567 was rejected.',
            ])
            ->create();
    }
}
