<?php

declare(strict_types=1);

use App\Enums\PageType;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the landing page with the qualification flow', function () {
    Page::factory()->create([
        'page_key' => 'landing',
        'page_type' => PageType::Landing,
        'slug' => '/',
        'is_active' => true,
    ]);

    $this->get('/')
        ->assertOk()
        ->assertSee('Find the Zoho stack')
        ->assertSeeLivewire('marketing.qualifier');
});

it('renders a product page with the consultation form', function () {
    Page::factory()->create([
        'page_key' => 'zoho_one',
        'page_type' => PageType::Product,
        'slug' => '/products/zoho-one',
        'is_active' => true,
    ]);

    $this->get('/products/zoho-one')
        ->assertOk()
        ->assertSee('Replace software sprawl')
        ->assertSeeLivewire('marketing.product-lead');
});

it('returns 404 for an inactive product page', function () {
    Page::factory()->inactive()->create([
        'page_key' => 'zoho_one',
        'page_type' => PageType::Product,
        'slug' => '/products/zoho-one',
    ]);

    $this->get('/products/zoho-one')->assertNotFound();
});

it('renders thank-you and legal pages', function () {
    Page::factory()->create([
        'page_key' => 'thanks',
        'page_type' => PageType::ThankYou,
        'slug' => '/thanks',
        'is_active' => true,
    ]);

    Page::factory()->create([
        'page_key' => 'privacy',
        'page_type' => PageType::Legal,
        'slug' => '/privacy',
        'is_active' => true,
    ]);

    Page::factory()->create([
        'page_key' => 'terms',
        'page_type' => PageType::Legal,
        'slug' => '/terms',
        'is_active' => true,
    ]);

    $this->get('/thanks')->assertOk()->assertSee('You are all set.');
    $this->get('/privacy')->assertOk()->assertSee('Privacy Policy');
    $this->get('/terms')->assertOk()->assertSee('Terms of Service');
});
