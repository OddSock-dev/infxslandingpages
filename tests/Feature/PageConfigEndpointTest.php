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

    public function test_renders_product_page_overrides_on_the_public_route(): void
    {
        Page::factory()->create([
            'page_key' => 'zoho_one',
            'page_type' => PageType::Product,
            'slug' => '/products/zoho-one',
            'is_active' => true,
            'seo_title' => 'Custom Zoho One | INFX',
            'meta_description' => 'All-in-one productivity suite.',
            'robots' => 'index, follow',
            'cta_text' => 'Book My Custom Consultation',
            'cta_url' => 'https://example.com/trial',
            'body_config' => ['offer_title' => 'Custom Offer Title'],
        ]);

        $this->get('/products/zoho-one')
            ->assertOk()
            ->assertSee('Custom Zoho One | INFX')
            ->assertSee('Book My Custom Consultation')
            ->assertSee('Custom Offer Title');
    }

    public function test_returns_404_for_an_inactive_product_page(): void
    {
        Page::factory()->inactive()->create([
            'page_key' => 'zoho_hidden',
            'page_type' => PageType::Product,
            'slug' => '/products/zoho-hidden',
        ]);

        $this->get('/products/zoho-hidden')
            ->assertNotFound();
    }

    public function test_returns_404_for_a_non_existent_product_slug(): void
    {
        $this->get('/products/does-not-exist')
            ->assertNotFound();
    }
}
