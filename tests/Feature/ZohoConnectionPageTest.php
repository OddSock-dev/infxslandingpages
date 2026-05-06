<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Filament\Pages\ZohoConnection;
use App\Filament\Widgets\ZohoConnectionOverviewWidget;
use App\Models\User;
use App\Models\ZohoCredential;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ZohoConnectionPageTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->actingAs($this->admin);
    }

    public function test_zoho_connection_page_renders_filament_sections(): void
    {
        $response = $this->get(ZohoConnection::getUrl());

        $response
            ->assertOk()
            ->assertSee('Connection settings')
            ->assertSee('Authorization checklist')
            ->assertSee('Save connection');
    }

    public function test_zoho_connection_overview_widget_renders_summary_stats(): void
    {
        ZohoCredential::set(ZohoCredential::REFRESH_TOKEN, 'refresh-token');
        ZohoCredential::set(ZohoCredential::ACCESS_TOKEN, 'access-token', CarbonImmutable::now()->addHour());

        Livewire::test(ZohoConnectionOverviewWidget::class)
            ->assertOk()
            ->assertSee('Connection')
            ->assertSee('Access Token')
            ->assertSee('Authorization Flow');
    }
}
