<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\PageType;
use App\Models\Journey;
use App\Models\JourneyAnswer;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_renders_the_landing_page_with_the_qualification_flow(): void
    {
        Page::factory()->create([
            'page_key' => 'landing',
            'page_type' => PageType::Landing,
            'slug' => '/',
            'is_active' => true,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Find the Zoho setup')
            ->assertSee('Answer a few simple questions about your business')
            ->assertSee('Find Your Best Zoho Option')
            ->assertSee('Start the questionnaire')
            ->assertSee('What are you trying to improve first?')
            ->assertSee('Zoho One')
            ->assertSee('/products/zoho-one')
            ->assertSee('/products/zoho-marketing-plus')
            ->assertSee('/products/zoho-workplace')
            ->assertDontSee('5-minute guided match')
            ->assertDontSee('Compare Zoho One, Marketing Plus, and Workplace with less guesswork')
            ->assertDontSee('questions to guide your choice')
            ->assertDontSee('Zoho Authorised Partner')
            ->assertDontSee('Will the next page still ask a few questions?');
    }

    public function test_renders_a_product_page_with_the_gated_next_step_flow(): void
    {
        Page::factory()->create([
            'page_key' => 'zoho_one',
            'page_type' => PageType::Product,
            'slug' => '/products/zoho-one',
            'is_active' => true,
        ]);

        $this->get('/products/zoho-one')
            ->assertOk()
            ->assertSee('Run the business from one connected system')
            ->assertSee('Quick Product Questions')
            ->assertDontSee('Start Free Trial');
    }

    public function test_routed_product_pages_show_recommendation_copy_and_unlocked_ctas(): void
    {
        Page::factory()->create([
            'page_key' => 'zoho_one',
            'page_type' => PageType::Product,
            'slug' => '/products/zoho-one',
            'is_active' => true,
        ]);

        $journey = Journey::factory()->routed('zoho_one')->create();

        JourneyAnswer::factory()->create([
            'journey_id' => $journey->id,
            'step_key' => 'diagnostic_1',
            'field_key' => 'primary_goal',
            'value' => 'unify_operations',
        ]);

        JourneyAnswer::factory()->create([
            'journey_id' => $journey->id,
            'step_key' => 'diagnostic_2',
            'field_key' => 'biggest_gap',
            'value' => 'manual_handoffs',
        ]);

        JourneyAnswer::factory()->create([
            'journey_id' => $journey->id,
            'step_key' => 'diagnostic_3',
            'field_key' => 'team_shape',
            'value' => 'multi_department_growth',
        ]);

        $this->get('/products/zoho-one?t='.$journey->journey_token)
            ->assertOk()
            ->assertSee('Based on what you shared, Zoho One looks like a strong fit for your business. Here is why:')
            ->assertSee('Request Consultation')
            ->assertSee('Start Free Trial')
            ->assertSee('https://store.zoho.com/ResellerCustomerSignUp.do?id=b90ffafff590634f12c003a7325340d7');
    }

    public function test_returns_404_for_an_inactive_product_page(): void
    {
        Page::factory()->inactive()->create([
            'page_key' => 'zoho_one',
            'page_type' => PageType::Product,
            'slug' => '/products/zoho-one',
        ]);

        $this->get('/products/zoho-one')->assertNotFound();
    }

    public function test_renders_thank_you_and_legal_pages(): void
    {
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
    }
}
