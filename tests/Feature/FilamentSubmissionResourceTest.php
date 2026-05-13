<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Filament\Resources\Submissions\Pages\ListSubmissions;
use App\Models\CrmSyncAttempt;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FilamentSubmissionResourceTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();

        $this->actingAs($this->admin);
    }

    public function test_list_submissions_renders_when_latest_sync_attempt_is_eager_loaded(): void
    {
        $submission = Submission::factory()->failed()->create();

        CrmSyncAttempt::factory()
            ->for($submission)
            ->failed('HTTP_500_SERVER_ERROR', 'Server error')
            ->create();

        Livewire::test(ListSubmissions::class)->assertOk();
    }
}
