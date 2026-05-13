<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Filament\Resources\CrmSyncAttempts\Pages\ListCrmSyncAttempts;
use App\Filament\Resources\CrmSyncAttempts\Pages\ViewCrmSyncAttempt;
use App\Filament\Resources\Submissions\Pages\ViewSubmission;
use App\Models\CrmSyncAttempt;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FilamentCrmSyncAttemptResourceTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();

        $this->actingAs($this->admin);
    }

    public function test_list_crm_sync_attempts_renders_with_requeue_visibility_callbacks(): void
    {
        $submission = Submission::factory()->failed()->create();

        CrmSyncAttempt::factory()
            ->for($submission)
            ->failed('HTTP_500_SERVER_ERROR', 'Server error')
            ->create();

        Livewire::test(ListCrmSyncAttempts::class)->assertOk();
    }

    public function test_view_crm_sync_attempt_renders_with_requeue_action_callbacks(): void
    {
        $attempt = $this->latestFailedAttempt();

        Livewire::test(ViewCrmSyncAttempt::class, ['record' => $attempt->id])->assertOk();
    }

    public function test_view_submission_renders_with_requeue_action_callbacks(): void
    {
        $attempt = $this->latestFailedAttempt();

        Livewire::test(ViewSubmission::class, ['record' => $attempt->submission_id])->assertOk();
    }

    private function latestFailedAttempt(): CrmSyncAttempt
    {
        $submission = Submission::factory()->failed()->create();

        return CrmSyncAttempt::factory()
            ->for($submission)
            ->failed('HTTP_500_SERVER_ERROR', 'Server error')
            ->create();
    }
}
