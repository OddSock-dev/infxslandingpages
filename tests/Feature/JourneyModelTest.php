<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JourneyStatus;
use App\Models\Journey;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;
use Tests\TestCase;

class JourneyModelTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Date::setTestNow();

        parent::tearDown();
    }

    public function test_it_assigns_an_expiry_when_created_without_one(): void
    {
        $now = CarbonImmutable::create(2026, 5, 7, 12, 0, 0, 'UTC');
        Date::setTestNow($now);

        $journey = Journey::create([
            'source_page_key' => 'landing',
            'status' => JourneyStatus::Qualifying,
        ])->fresh();

        $this->assertInstanceOf(Journey::class, $journey);
        $this->assertInstanceOf(CarbonImmutable::class, $journey->expires_at);
        $this->assertTrue($journey->expires_at->equalTo($now->addHours(24)));
    }
}