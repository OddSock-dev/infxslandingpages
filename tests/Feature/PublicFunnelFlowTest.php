<?php

declare(strict_types=1);

use App\Enums\JourneyStatus;
use App\Jobs\SyncSubmissionToZohoJob;
use App\Models\Journey;
use App\Models\Submission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('creates a routed journey from the public qualifier component', function () {
    Livewire::test('marketing.qualifier')
        ->set('selectedProduct', 'zoho_marketing_plus')
        ->set('consent', true)
        ->call('qualify')
        ->assertRedirect();

    $journey = Journey::query()->firstOrFail();

    expect($journey->status)->toBe(JourneyStatus::Routed)
        ->and($journey->assigned_product_key)->toBe('zoho_marketing_plus');
});

it('submits a consultation request from the public product lead component', function () {
    Queue::fake();

    $journey = Journey::factory()->routed('zoho_one')->create();

    Livewire::withQueryParams(['t' => $journey->journey_token])
        ->test('marketing.product-lead', ['pageKey' => 'zoho_one', 'productName' => 'Zoho One'])
        ->set('name', 'Jane Doe')
        ->set('email', 'jane@example.com')
        ->set('phone', '+27211234567')
        ->set('company', 'ACME Ltd')
        ->call('submit')
        ->assertRedirect();

    $submission = Submission::query()->firstOrFail();

    expect($submission->journey_id)->toBe($journey->id)
        ->and($submission->product_key)->toBe('zoho_one');

    Queue::assertPushed(SyncSubmissionToZohoJob::class);
});
