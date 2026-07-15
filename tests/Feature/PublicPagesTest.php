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
            ->assertSee('Stop losing time to disconnected tools')
            ->assertSee('Answer four practical questions')
            ->assertSee('Your Zoho Recommendation')
            ->assertSee('Start the questionnaire')
            ->assertSee('What are you trying to improve first?')
            ->assertSee('max-w-124', false)
            ->assertSee('lg:absolute', false)
            ->assertSee('lg:top-112', false)
            ->assertSee('media/OpsTeam.webp', false)
            ->assertSee('inset-x-0 top-0', false)
            ->assertDontSee('bottom-0', false)
            ->assertDontSee('lg:-translate-y-52', false)
            ->assertSee('Zoho One')
            ->assertSee('/products/zoho-one')
            ->assertSee('/products/zoho-marketing-plus')
            ->assertSee('/products/zoho-workplace')
            ->assertDontSee('5-minute guided match')
            ->assertDontSee('Compare Zoho One, Marketing Plus, and Workplace with less guesswork')
            ->assertDontSee('questions to guide your choice')
            ->assertSee('Zoho Authorized Partner')
            ->assertDontSee('Will the next page still ask a few questions?');
    }

    public function test_renders_a_product_page_with_the_gated_next_step_flow(): void
    {
        $pages = [
            'zoho_one' => [
                'slug' => '/products/zoho-one',
                'headline' => 'Run your whole business from one connected system',
                'hero_image' => 'media/OpsTeam.webp',
                'content_media' => [
                    'media/zoho-one-connected-operations-v2.webp',
                    'media/zoho-one-content-planning.webp',
                    'media/zoho-one-leadership-visibility-v2.webp',
                ],
            ],
            'zoho_marketing_plus' => [
                'slug' => '/products/zoho-marketing-plus',
                'headline' => 'Run every campaign from one smarter marketing workspace',
                'hero_image' => 'media/MarketingTeam.webp',
                'content_media' => [
                    'media/marketing-plus-campaign-team-v2.webp',
                    'media/marketing-plus-content-support.webp',
                    'media/marketing-plus-performance-visibility-v2.webp',
                ],
            ],
            'zoho_workplace' => [
                'slug' => '/products/zoho-workplace',
                'headline' => 'Give your team one calmer workspace',
                'hero_image' => 'media/WorkspaceTeam.webp',
                'content_media' => [],
            ],
        ];

        foreach ($pages as $pageKey => $productPage) {
            Page::factory()->create([
                'page_key' => $pageKey,
                'page_type' => PageType::Product,
                'slug' => $productPage['slug'],
                'is_active' => true,
            ]);

            $response = $this->get($productPage['slug'])
                ->assertOk()
                ->assertSee($productPage['headline'])
                ->assertSee('Why teams choose', false)
                ->assertDontSee('Why this page stands alone')
                ->assertSee('Two-Minute Fit Check')
                ->assertSee($productPage['hero_image'], false)
                ->assertSee('inset-x-0 top-0', false)
                ->assertDontSee('bottom-0', false)
                ->assertSee('lg:grid-cols-[minmax(0,0.96fr)_minmax(21rem,30rem)]', false)
                ->assertSee('xl:top-116', false)
                ->assertDontSee('Start Free Trial');

            foreach ($productPage['content_media'] as $contentMedia) {
                $response->assertSee($contentMedia, false);
            }
        }
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
