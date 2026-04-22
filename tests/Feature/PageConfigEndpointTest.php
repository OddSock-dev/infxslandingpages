<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\PageType;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageConfigEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_full_page_config_for_an_active_page(): void
    {
        Page::factory()->create([
            'page_key' => 'zoho_one',
            'page_type' => PageType::Product,
            'slug' => '/products/zoho-one',
            'is_active' => true,
            'seo_title' => 'Zoho One | INFX',
            'meta_description' => 'All-in-one productivity suite.',
            'robots' => 'index, follow',
            'cta_text' => 'Get a Free Trial',
            'cta_url' => 'https://example.com/trial',
            'body_config' => ['hero_headline' => 'Scale your business'],
        ]);

        $this->getJson('/api/config/pages/zoho_one')
            ->assertOk()
            ->assertJsonPath('page_key', 'zoho_one')
            ->assertJsonPath('page_type', 'product')
            ->assertJsonPath('slug', '/products/zoho-one')
            ->assertJsonPath('seo_title', 'Zoho One | INFX')
            ->assertJsonPath('robots', 'index, follow')
            ->assertJsonPath('cta_text', 'Get a Free Trial')
            ->assertJsonPath('body_config.hero_headline', 'Scale your business');
    }

    public function test_returns_404_for_an_inactive_page(): void
    {
        Page::factory()->inactive()->create(['page_key' => 'zoho_hidden', 'slug' => '/hidden']);

        $this->getJson('/api/config/pages/zoho_hidden')
            ->assertNotFound();
    }

    public function test_returns_404_for_a_non_existent_page_key(): void
    {
        $this->getJson('/api/config/pages/does_not_exist')
            ->assertNotFound();
    }
}
