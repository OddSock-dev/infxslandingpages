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
            ->assertSee('/products/zoho_one/start-trial?')
            ->assertSee('start_free_trial_click');
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

    public function test_exposes_a_sitemap_for_indexable_public_pages(): void
    {
        Page::factory()->inactive()->create([
            'page_key' => 'zoho_workplace',
            'page_type' => PageType::Product,
            'slug' => '/products/zoho-workplace',
        ]);

        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $response->assertSee(url('/'), false);
        $response->assertSee(url('/products/zoho-one'), false);
        $response->assertSee(url('/products/zoho-marketing-plus'), false);
        $response->assertSee(url('/privacy'), false);
        $response->assertSee(url('/terms'), false);
        $response->assertDontSee(url('/thanks'), false);
        $response->assertDontSee(url('/products/zoho-workplace'), false);
    }

    public function test_exposes_robots_with_the_sitemap_url(): void
    {
        $response = $this->get('/robots.txt');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
        $response->assertContent(implode(PHP_EOL, [
            'User-agent: facebookexternalhit',
            'Allow: /',
            '',
            'User-agent: Twitterbot',
            'Allow: /',
            '',
            'User-agent: LinkedInBot',
            'Allow: /',
            '',
            'User-agent: Slackbot',
            'Allow: /',
            '',
            'User-agent: WhatsApp',
            'Allow: /',
            '',
            'User-agent: Discordbot',
            'Allow: /',
            '',
            'User-agent: TelegramBot',
            'Allow: /',
            '',
            '# AI search & LLM crawlers — explicit opt-in for GEO/AEO indexing',
            'User-agent: GPTBot',
            'Allow: /',
            '',
            'User-agent: ChatGPT-User',
            'Allow: /',
            '',
            'User-agent: OAI-SearchBot',
            'Allow: /',
            '',
            'User-agent: ClaudeBot',
            'Allow: /',
            '',
            'User-agent: anthropic-ai',
            'Allow: /',
            '',
            'User-agent: PerplexityBot',
            'Allow: /',
            '',
            'User-agent: Amazonbot',
            'Allow: /',
            '',
            'User-agent: meta-externalagent',
            'Allow: /',
            '',
            'User-agent: Meta-ExternalFetcher',
            'Allow: /',
            '',
            'User-agent: YouBot',
            'Allow: /',
            '',
            'User-agent: Applebot-Extended',
            'Allow: /',
            '',
            'User-agent: cohere-ai',
            'Allow: /',
            '',
            'User-agent: Diffbot',
            'Allow: /',
            '',
            'User-agent: Bytespider',
            'Allow: /',
            '',
            '# General crawlers',
            'User-agent: *',
            'Allow: /',
            '',
            'Sitemap: '.url('/sitemap.xml'),
        ]));
    }

    public function test_it_renders_a_single_ga4_marker_when_analytics_is_enabled(): void
    {
        config()->set('services.analytics.enabled', true);
        config()->set('services.analytics.ga4_measurement_id', 'G-TEST1234');

        Page::factory()->create([
            'page_key' => 'landing',
            'page_type' => PageType::Landing,
            'slug' => '/',
            'is_active' => true,
        ]);

        $response = $this->get('/');
        $content = $response->getContent();

        $response->assertOk();
        self::assertIsString($content);
        self::assertSame(1, substr_count($content, 'window.infxBootGa4 = function ()'));
        self::assertStringContainsString(
            'https://www.googletagmanager.com/gtag/js?id=G-TEST1234',
            $content,
        );
    }
}
