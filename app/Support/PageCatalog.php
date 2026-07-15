<?php

declare(strict_types=1);

namespace App\Support;

use App\Enums\PageType;
use App\Models\Page;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * @phpstan-type PageCatalogDefinition array{
 *     page_key: string,
 *     page_type: PageType,
 *     slug: string,
 *     is_active: bool,
 *     seo_title: ?string,
 *     meta_description: ?string,
 *     og_title: ?string,
 *     og_description: ?string,
 *     og_image_url: ?string,
 *     robots: string,
 *     cta_text: ?string,
 *     cta_url: ?string,
 *     trial_url: ?string,
 *     body_config: array<array-key, mixed>
 * }
 * @phpstan-type ProductNavigationItem array{page_key: string, label: string, slug: string}
 */
class PageCatalog
{
    /**
     * @return array<string, PageCatalogDefinition>
     */
    public static function definitions(): array
    {
        return [
            'landing' => [
                'page_key' => 'landing',
                'page_type' => PageType::Landing,
                'slug' => '/',
                'is_active' => true,
                'seo_title' => 'Find the Right Zoho Solution for Your Business | INFX Solutions',
                'meta_description' => 'Get a clear Zoho recommendation in about two minutes, with practical guidance from a South African Zoho Authorized Partner.',
                'og_title' => 'INFX Solutions | Zoho Growth Partner',
                'og_description' => 'Answer four quick questions, get a clearer Zoho starting point, and choose a free trial or local implementation guidance.',
                'og_image_url' => null,
                'robots' => 'index, follow',
                'cta_text' => 'Get My Zoho Recommendation',
                'cta_url' => '#qualify',
                'trial_url' => null,
                'body_config' => [
                    'eyebrow' => 'A clearer Zoho decision in about two minutes',
                    'headline' => 'Stop losing time to disconnected tools. Find the right Zoho starting point.',
                    'subheadline' => 'Answer four practical questions and get a recommendation shaped around your goals, team, and timing—then choose a free trial or local guidance from INFX.',
                    'hero_badges' => [
                        '5-minute guided match',
                        'Built for South African SMMEs',
                        'Trial-ready recommendations',
                    ],
                    'hero_points' => [
                        'See which Zoho option suits your business needs',
                        'Compare Zoho One, Marketing Plus, and Workplace with less guesswork',
                        'Qualify once, then move into the right free trial or a guided consultation',
                    ],
                    'hero_metrics' => [
                        ['value' => '15', 'label' => 'questions to guide your choice'],
                        ['value' => '3', 'label' => 'Zoho options to explore'],
                        ['value' => '24h', 'label' => 'reply window'],
                        ['value' => 'POPIA', 'label' => 'privacy-aware support'],
                    ],
                    'hero_logo_block' => [
                        'label' => 'Explore these Zoho options',
                        'items' => [
                            [
                                'src' => '/brand/zoho-one.svg',
                                'alt' => 'Zoho One',
                                'title' => 'Zoho One',
                                'description' => 'Run sales, service, finance, and operations from one connected business suite.',
                                'url' => '/products/zoho-one',
                            ],
                            [
                                'src' => '/brand/zoho-marketing-plus.svg',
                                'alt' => 'Zoho Marketing Plus',
                                'title' => 'Zoho Marketing Plus',
                                'description' => 'Coordinate campaigns, journeys, events, and reporting in one marketing workspace.',
                                'url' => '/products/zoho-marketing-plus',
                            ],
                            [
                                'src' => '/brand/zoho-workplace.svg',
                                'alt' => 'Zoho Workplace',
                                'title' => 'Zoho Workplace',
                                'description' => 'Bring email, chat, docs, and meetings into one calmer collaboration suite.',
                                'url' => '/products/zoho-workplace',
                            ],
                        ],
                    ],
                    'trust_heading' => 'A useful recommendation, not another generic sales form.',
                    'trust_copy' => 'You will see a clear starting point immediately, with a South African Zoho Authorized Partner available when you want practical help.',
                    'trust_blocks' => [
                        [
                            'icon' => 'shield',
                            'title' => 'About two minutes',
                            'description' => 'Four focused questions keep the process quick and give the recommendation useful context.',
                        ],
                        [
                            'icon' => 'compass',
                            'title' => 'Clearer choices',
                            'description' => 'See the difference between operations, marketing, and collaboration tools without getting lost in product jargon.',
                        ],
                        [
                            'icon' => 'bolt',
                            'title' => 'Local expertise, no obligation',
                            'description' => 'Use the recommendation yourself or ask an INFX specialist for practical setup and migration guidance.',
                        ],
                    ],
                    'spotlight' => [
                        'eyebrow' => 'Start with what needs attention most',
                        'title' => 'Start with the business problem, not a 45-app product list.',
                        'copy' => 'The fit check narrows the decision around the outcome you need most: connected operations, clearer marketing performance, or calmer team collaboration.',
                        'media_url' => 'media/home-systems-review-v2.webp',
                        'media_alt' => 'South African business leaders reviewing a systems workflow together',
                    ],
                    'journey_steps' => [
                        [
                            'title' => 'Tell us what is frustrating the team',
                            'description' => 'Share what needs attention most so we can focus on the kind of Zoho solution that will help.',
                        ],
                        [
                            'title' => 'See where to start',
                            'description' => 'We will point you to the Zoho option that best matches your goals, team, and timing.',
                        ],
                        [
                            'title' => 'Choose what you want to do next',
                            'description' => 'You can continue to the suggested product page, qualify for a free trial, or speak to INFX when you want guidance.',
                        ],
                    ],
                    'logo_bank' => [
                        'title' => 'Three Zoho options. One clearer choice.',
                        'copy' => 'Whether you need better operations, stronger marketing, or calmer collaboration, you can explore the option that matches your business.',
                        'items' => [
                            [
                                'src' => '/brand/zoho-one.svg',
                                'alt' => 'Zoho One',
                                'title' => 'Zoho One',
                                'description' => 'For businesses that want sales, service, finance, and operations working from one connected place.',
                                'media_type' => 'image',
                                'media_url' => 'media/zoho-one-operations-team-v2.webp',
                                'media_alt' => 'Operations leaders planning a connected business workflow',
                                'url' => '/products/zoho-one',
                            ],
                            [
                                'src' => '/brand/zoho-marketing-plus.svg',
                                'alt' => 'Zoho Marketing Plus',
                                'title' => 'Zoho Marketing Plus',
                                'description' => 'For teams that want cleaner campaign coordination, better reporting, and smoother customer communication.',
                                'media_type' => 'image',
                                'media_url' => 'brand/zoho-marketing-plus.svg',
                                'media_alt' => 'Zoho Marketing Plus product icon',
                                'media_fit' => 'contain',
                                'url' => '/products/zoho-marketing-plus',
                            ],
                            [
                                'src' => '/brand/zoho-workplace.svg',
                                'alt' => 'Zoho Workplace',
                                'title' => 'Zoho Workplace',
                                'description' => 'For teams modernising email, collaboration, meetings, and knowledge sharing in one privacy-first suite.',
                                'media_type' => 'video',
                                'media_url' => 'media/zoho-workplace-happiness.mp4',
                                'media_alt' => 'Zoho Workplace collaboration preview',
                                'url' => '/products/zoho-workplace',
                            ],
                        ],
                    ],
                    'outcome_heading' => 'What this helps you do sooner.',
                    'outcome_copy' => 'You get a clearer idea of where to start, which Zoho product is worth trialling first, and what to ask for when you want expert help.',
                    'outcomes' => [
                        [
                            'metric' => '1',
                            'title' => 'Clearer next step',
                            'description' => 'You do not have to figure out a 45-app ecosystem on your own before you know what to look at next.',
                        ],
                        [
                            'metric' => '4',
                            'title' => 'Business priorities covered',
                            'description' => 'Operations, marketing, collaboration, and timing questions keep the conversation focused on what matters most.',
                        ],
                        [
                            'metric' => '24h',
                            'title' => 'Quicker expert support',
                            'description' => 'When you reach out, our team already understands the basics and can respond with useful advice faster.',
                        ],
                        [
                            'metric' => '0',
                            'title' => 'Less wasted time',
                            'description' => 'A short check helps you avoid wasted time and focus on the option that best fits your needs.',
                        ],
                    ],
                    'feature_heading' => 'Why businesses choose INFX to help them decide',
                    'feature_subheading' => 'You get clear advice, practical answers, and a simpler way to move forward.',
                    'features' => [
                        [
                            'icon' => 'spark',
                            'title' => 'Clear product guidance',
                            'description' => 'See the Zoho option that matches what your business actually needs.',
                        ],
                        [
                            'icon' => 'compass',
                            'title' => 'Faster buying confidence',
                            'description' => 'A few well-chosen questions can make the shortlist much clearer.',
                        ],
                        [
                            'icon' => 'shield',
                            'title' => 'You will not have to repeat yourself',
                            'description' => 'When you speak to us, we already understand the basics of what you need.',
                        ],
                        [
                            'icon' => 'bolt',
                            'title' => 'Local Zoho expertise',
                            'description' => 'Get practical setup, migration, and change support from a South African team that understands local businesses.',
                        ],
                    ],
                    'faq_heading' => 'Questions people usually ask before choosing a Zoho option.',
                    'faq_copy' => 'Here are a few quick answers to help you decide with confidence.',
                    'faq' => [
                        [
                            'question' => 'What does this quick check actually do?',
                            'answer' => 'It asks a few simple questions and points you to the Zoho option most likely to suit your business.',
                        ],
                        [
                            'question' => 'Do we have to commit to Zoho before talking to you?',
                            'answer' => 'No. The goal is to clarify fit first. The consultation helps you understand whether Zoho One, Marketing Plus, Workplace, or another option makes the most sense.',
                        ],
                        [
                            'question' => 'Is this suited to South African compliance requirements?',
                            'answer' => 'Yes. We handle your information carefully and work with South African business requirements in mind.',
                        ],
                    ],
                    'cta_heading' => 'Get a clearer Zoho starting point before you commit.',
                    'cta_copy' => 'Answer four practical questions, see your recommendation, and decide whether you want a free trial or a conversation with INFX.',
                    'cta_primary' => [
                        'label' => 'Get My Recommendation',
                        'url' => '#qualify',
                    ],
                    'cta_secondary' => [
                        'label' => 'See Zoho One',
                        'url' => '/products/zoho-one',
                    ],
                ],
            ],
            'zoho_one' => [
                'page_key' => 'zoho_one',
                'page_type' => PageType::Product,
                'slug' => '/products/zoho-one',
                'is_active' => true,
                'seo_title' => 'Zoho One for Fast-Growing Businesses | INFX Solutions',
                'meta_description' => 'Run sales, delivery, finance, service, and operations on one connected Zoho platform with local implementation from INFX Solutions.',
                'og_title' => 'Zoho One | INFX Solutions',
                'og_description' => 'Bring sales, service, finance, and operations together in one connected Zoho setup with practical help from INFX.',
                'og_image_url' => null,
                'robots' => 'index, follow',
                'cta_text' => 'Request a Zoho One Consultation',
                'cta_url' => '#consultation',
                'trial_url' => 'https://store.zoho.com/ResellerCustomerSignUp.do?id=b90ffafff590634f12c003a7325340d7',
                'body_config' => [
                    'nav_label' => 'Zoho One',
                    'eyebrow' => 'One Connected Zoho Suite. 45+ Apps.',
                    'headline' => 'Run your whole business from one connected system',
                    'subheadline' => 'Zoho One helps growing businesses bring sales, delivery, finance, service, and reporting into one connected setup so growth creates less admin and more clarity.',
                    'hero_badges' => [
                        'Qualify to unlock a free trial',
                        'No card required',
                        'Setup help available',
                    ],
                    'hero_points' => [
                        'One system for sales, marketing, finance, operations, and service',
                        'Real-time visibility across the business instead of disconnected reporting',
                        'Qualify below to unlock a free trial and bring INFX in for setup, migration, and onboarding support',
                    ],
                    'hero_metrics' => [
                        ['value' => '45+', 'label' => 'Integrated business apps'],
                        ['value' => '1', 'label' => 'Connected business setup'],
                        ['value' => '24h', 'label' => 'response time'],
                    ],
                    'hero_logo_block' => [
                        'label' => 'Built for businesses ready to scale properly',
                        'items' => [
                            [
                                'src' => '/brand/zoho-one.svg',
                                'alt' => 'Zoho One',
                                'title' => 'Zoho One',
                                'description' => 'A connected business suite for teams that want one clearer operating system as they scale.',
                            ],
                        ],
                    ],
                    'trust_heading' => 'Why Zoho One works when growth has outpaced the systems underneath it.',
                    'trust_copy' => 'Zoho One suits businesses that want less duplication, less manual work, and a clearer view of how the whole business is performing.',
                    'trust_blocks' => [
                        [
                            'icon' => 'layers',
                            'title' => 'One clear view',
                            'description' => 'Replace fragmented reporting and duplicated data with one connected place to work.',
                        ],
                        [
                            'icon' => 'gear',
                            'title' => 'Expert setup support',
                            'description' => 'INFX helps you plan the setup, integrations, and training around the way your business works.',
                        ],
                        [
                            'icon' => 'chart',
                            'title' => 'Executive visibility',
                            'description' => 'Bring revenue, delivery, finance, and operational health into a more usable reporting layer.',
                        ],
                    ],
                    'spotlight' => [
                        'media_type' => 'image',
                        'media_url' => 'media/zoho-one-operations-team-v2.webp',
                        'media_alt' => 'South African operations team planning a connected business workflow',
                    ],
                    'offer_title' => 'What Zoho One unlocks',
                    'offer_copy' => 'Ideal for businesses that have outgrown separate tools and want sales, service, finance, and operations working together. Complete the quick fit check to see whether a trial or a guided rollout conversation is the better next step.',
                    'highlights' => [
                        'Sales and CRM visibility',
                        'Project and service delivery tracking',
                        'Finance and invoicing workflows',
                        'Cross-team reporting and automation',
                    ],
                    'logo_bank' => [
                        'title' => 'See what a more connected business can look like.',
                        'copy' => 'These visuals show how Zoho One brings your core tools together so work feels easier to manage.',
                        'items' => [
                            [
                                'src' => '/brand/zoho-one.svg',
                                'alt' => 'Zoho One',
                                'title' => 'One place for the whole business',
                                'description' => 'Bring sales, finance, delivery, and service into one environment instead of stitching disconnected apps together.',
                                'media_type' => 'image',
                                'media_url' => 'media/zoho-one-connected-operations-v2.webp',
                                'media_alt' => 'South African cross-functional team mapping a connected business workflow',
                            ],
                            [
                                'src' => '/brand/zoho-authorized-partner.webp',
                                'alt' => 'Zoho Authorized Partner',
                                'title' => 'Setup and migration support',
                                'description' => 'Move from disconnected tools to a more connected way of working with practical help from INFX.',
                                'media_type' => 'image',
                                'media_url' => 'media/zoho-one-content-planning.webp',
                                'media_alt' => 'Planning notes beside a laptop during an operations workshop',
                            ],
                            [
                                'src' => '/brand/zoho-one.svg',
                                'alt' => 'Zoho One',
                                'title' => 'Visibility that reaches leadership',
                                'description' => 'See sales, finance, service, and operations more clearly in one place.',
                                'media_type' => 'image',
                                'media_url' => 'media/zoho-one-leadership-visibility-v2.webp',
                                'media_alt' => 'South African leaders reviewing unified business performance',
                            ],
                        ],
                    ],
                    'outcome_heading' => 'What changes after you implement Zoho One properly.',
                    'outcome_copy' => 'The biggest benefit is not the number of apps. It is how much easier the business becomes to run when information lives in one place.',
                    'outcomes' => [
                        [
                            'metric' => '1',
                            'title' => 'A simpler way to run the business',
                            'description' => 'Replace scattered systems with one clearer way to run the business.',
                        ],
                        [
                            'metric' => '45+',
                            'title' => 'Apps in one place',
                            'description' => 'Start where the pain is highest, then expand without re-platforming every year.',
                        ],
                        [
                            'metric' => '↓',
                            'title' => 'Less manual work',
                            'description' => 'Reduce duplicated work, disconnected processes, and spreadsheet-driven reporting.',
                        ],
                        [
                            'metric' => '↑',
                            'title' => 'Faster decision-making',
                            'description' => 'Give leaders clearer signals across sales, service, finance, and delivery.',
                        ],
                    ],
                    'feature_heading' => 'Why buyers choose Zoho One',
                    'feature_subheading' => 'The value is not just the app count. It is the way those apps remove friction between teams.',
                    'features' => [
                        [
                            'icon' => 'layers',
                            'title' => 'Shared data foundation',
                            'description' => 'Information moves between apps more smoothly without relying on a messy patchwork of extra connectors.',
                        ],
                        [
                            'icon' => 'chart',
                            'title' => 'Executive visibility',
                            'description' => 'See pipeline, delivery, support, and finance signals together instead of in separate dashboards.',
                        ],
                        [
                            'icon' => 'gear',
                            'title' => 'Process automation',
                            'description' => 'Automate approvals, tasks, alerts, and cross-team workflows as the business matures.',
                        ],
                        [
                            'icon' => 'rocket',
                            'title' => 'Room to grow',
                            'description' => 'Start with the apps you need now and expand inside the same ecosystem as requirements evolve.',
                        ],
                    ],
                    'faq_heading' => 'Questions we hear before a business moves more of its work into Zoho One.',
                    'faq_copy' => 'The main question is usually not whether Zoho One can do the job. It is whether the move will feel practical for the business.',
                    'faq' => [
                        [
                            'question' => 'Is Zoho One only for large enterprises?',
                            'answer' => 'No. It is especially strong for growing businesses that want one scalable foundation instead of re-platforming every year.',
                        ],
                        [
                            'question' => 'Can we implement in phases?',
                            'answer' => 'Yes. We can phase the move so you start where the benefit is biggest and expand from there at a sensible pace.',
                        ],
                        [
                            'question' => 'Can you migrate our existing data?',
                            'answer' => 'Yes. We handle structured migrations from CRMs, spreadsheets, support tools, finance systems, and collaboration platforms.',
                        ],
                        [
                            'question' => 'Can we start with a free trial before planning the rollout?',
                            'answer' => 'Yes. The quick fit check helps you choose between starting a free trial or speaking to INFX first about setup, migration, or expansion.',
                        ],
                    ],
                    'cta_heading' => 'See whether Zoho One deserves a closer look.',
                    'cta_copy' => 'Complete the quick fit check, then choose a free trial or ask INFX for rollout advice before you begin.',
                ],
            ],
            'zoho_marketing_plus' => [
                'page_key' => 'zoho_marketing_plus',
                'page_type' => PageType::Product,
                'slug' => '/products/zoho-marketing-plus',
                'is_active' => true,
                'seo_title' => 'Zoho Marketing Plus for Unified Growth Teams | INFX Solutions',
                'meta_description' => 'Bring campaigns, social, webinars, events, and automations together so your marketing team can work from one place with INFX.',
                'og_title' => 'Zoho Marketing Plus | INFX Solutions',
                'og_description' => 'A unified marketing platform for teams that need clarity, coordination, and conversion.',
                'og_image_url' => null,
                'robots' => 'index, follow',
                'cta_text' => 'Plan My Marketing Plus Rollout',
                'cta_url' => '#consultation',
                'trial_url' => 'https://store.zoho.com/ResellerCustomerSignUp.do?id=cb03293c4f696fa1058bd992189acdee',
                'body_config' => [
                    'nav_label' => 'Zoho Marketing Plus',
                    'eyebrow' => 'One Home For Campaigns, Journeys, Events, and Attribution',
                    'headline' => 'Run every campaign from one smarter marketing workspace',
                    'subheadline' => 'Zoho Marketing Plus gives your team one workspace for campaigns, automations, events, webinars, analytics, and assets instead of switching between disconnected tools all day.',
                    'hero_badges' => [
                        'Qualify to unlock a free trial',
                        'No card required',
                        'Implementation strategy support',
                    ],
                    'hero_points' => [
                        'Campaign planning, social, webinars, events, and automations in one place',
                        'Clearer reporting, cleaner collaboration, and stronger budget visibility',
                        'Qualify below to unlock a free trial and get campaign setup guidance from INFX',
                    ],
                    'hero_metrics' => [
                        ['value' => '40%', 'label' => 'Typical engagement lift after campaign clean-up'],
                        ['value' => '1', 'label' => 'Unified campaign workspace'],
                        ['value' => '0', 'label' => 'Tolerance for tool sprawl'],
                    ],
                    'hero_logo_block' => [
                        'label' => 'Ideal for teams that want clearer marketing performance',
                        'items' => [
                            [
                                'src' => '/brand/zoho-marketing-plus.svg',
                                'alt' => 'Zoho Marketing Plus',
                                'title' => 'Zoho Marketing Plus',
                                'description' => 'A unified marketing workspace for campaigns, journeys, events, and reporting.',
                            ],
                        ],
                    ],
                    'trust_heading' => 'Why marketing teams choose Marketing Plus.',
                    'trust_copy' => 'It helps teams plan, launch, measure, and improve campaigns without bouncing between disconnected tools.',
                    'trust_blocks' => [
                        [
                            'icon' => 'megaphone',
                            'title' => 'Cross-channel planning',
                            'description' => 'Bring campaigns, content, events, and execution into one coordinated view.',
                        ],
                        [
                            'icon' => 'pulse',
                            'title' => 'Attribution clarity',
                            'description' => 'See what is driving real interest instead of guessing which channels are working.',
                        ],
                        [
                            'icon' => 'route',
                            'title' => 'Marketing automation',
                            'description' => 'Build helpful automations without having to stitch several tools together.',
                        ],
                    ],
                    'spotlight' => [
                        'media_type' => 'image',
                        'media_url' => 'media/marketing-plus-campaign-team-v2.webp',
                        'media_alt' => 'South African marketing team planning a coordinated campaign',
                    ],
                    'offer_title' => 'What Zoho Marketing Plus unlocks',
                    'offer_copy' => 'Best suited to active marketing teams that need clearer reporting, easier coordination, and more consistent campaigns. Complete the quick fit check to choose between hands-on trial access and a guided campaign-planning conversation.',
                    'highlights' => [
                        'Cross-channel campaign planning',
                        'Marketing automation and nurture design',
                        'Webinar and event management',
                        'Attribution that informs better budget choices',
                    ],
                    'logo_bank' => [
                        'title' => 'See a more connected marketing workspace.',
                        'copy' => 'These visuals show how Marketing Plus can bring planning, performance, and day-to-day marketing work closer together before you start your free trial.',
                        'items' => [
                            [
                                'src' => '/brand/zoho-marketing-plus.svg',
                                'alt' => 'Zoho Marketing Plus',
                                'title' => 'A unified campaign workspace',
                                'description' => 'Keep planning, execution, and reporting closer together so the team can move faster with more control.',
                                'media_type' => 'image',
                                'media_url' => 'media/marketing-plus-campaign-team-v2.webp',
                                'media_alt' => 'South African marketing team planning a coordinated campaign',
                            ],
                            [
                                'src' => '/brand/zoho-authorized-partner.webp',
                                'alt' => 'Zoho Authorized Partner',
                                'title' => 'Support from INFX',
                                'description' => 'Get help setting up campaigns, reports, and automations in a way that suits your team.',
                                'media_type' => 'image',
                                'media_url' => 'media/marketing-plus-content-support.webp',
                                'media_alt' => 'Campaign planning discussion around a laptop',
                            ],
                            [
                                'src' => '/brand/zoho-marketing-plus.svg',
                                'alt' => 'Zoho Marketing Plus',
                                'title' => 'Clearer performance visibility',
                                'description' => 'Turn channel and campaign activity into clearer reporting for marketing and revenue teams.',
                                'media_type' => 'image',
                                'media_url' => 'media/marketing-plus-performance-visibility-v2.webp',
                                'media_alt' => 'South African marketing and revenue leaders reviewing campaign performance',
                            ],
                        ],
                    ],
                    'outcome_heading' => 'What changes after the marketing team moves into one coordinated workspace.',
                    'outcome_copy' => 'Your team spends less time switching tools, gets clearer reporting, and has more confidence in what is working.',
                    'outcomes' => [
                        [
                            'metric' => '1',
                            'title' => 'Unified campaign workspace',
                            'description' => 'Bring the whole marketing operating rhythm into one platform instead of five disconnected tools.',
                        ],
                        [
                            'metric' => '↑',
                            'title' => 'Better campaign visibility',
                            'description' => 'Sharpen the connection between campaign activity, pipeline movement, and ROI.',
                        ],
                        [
                            'metric' => '↓',
                            'title' => 'Less execution drag',
                            'description' => 'Reduce switching costs across social, email, webinars, events, and reporting.',
                        ],
                        [
                            'metric' => '24h',
                            'title' => 'Quicker support from our team',
                            'description' => 'Share what you need and get useful support faster when you are ready to talk.',
                        ],
                    ],
                    'feature_heading' => 'Why growth teams move to Marketing Plus',
                    'feature_subheading' => 'It helps the marketing team plan, launch, and measure campaigns from one place.',
                    'features' => [
                        [
                            'icon' => 'megaphone',
                            'title' => 'Cross-channel coordination',
                            'description' => 'Run email, social, events, and nurture activity from one strategic layer instead of isolated campaign islands.',
                        ],
                        [
                            'icon' => 'route',
                            'title' => 'Automations that keep momentum going',
                            'description' => 'Build helpful automations across campaigns and customer follow-up without stitching tools together.',
                        ],
                        [
                            'icon' => 'pulse',
                            'title' => 'Clearer campaign reporting',
                            'description' => 'See which channels and campaigns are bringing in real opportunities.',
                        ],
                        [
                            'icon' => 'spark',
                            'title' => 'Faster campaign execution',
                            'description' => 'Reduce switching costs between tools so the team can launch more confidently and iterate faster.',
                        ],
                    ],
                    'faq_heading' => 'Questions marketing teams ask before moving campaign operations into one platform.',
                    'faq_copy' => 'Most teams already know they need better visibility. They mainly want to know whether the move will be worth it and how it fits with their current setup.',
                    'faq' => [
                        [
                            'question' => 'How is Marketing Plus different from separate email tools?',
                            'answer' => 'It helps you manage more than email in one place, including social, webinars, events, and automation, with clearer reporting across the board.',
                        ],
                        [
                            'question' => 'Can it connect to Zoho CRM?',
                            'answer' => 'Yes. It works particularly well when paired with Zoho CRM or the wider Zoho ecosystem, but can also fit into mixed environments.',
                        ],
                        [
                            'question' => 'Is it right for smaller teams?',
                            'answer' => 'Yes, especially when the cost of switching between separate marketing tools is already slowing the team down.',
                        ],
                        [
                            'question' => 'Can we start with a free trial before we redesign our campaigns?',
                            'answer' => 'Yes. The quick fit check helps you decide whether to start a free trial or bring INFX in first to refine journeys, reporting, or campaign structure.',
                        ],
                    ],
                    'cta_heading' => 'See whether Marketing Plus fits the way your team actually works.',
                    'cta_copy' => 'Complete the quick fit check, then choose a free trial or ask INFX for campaign guidance first.',
                ],
            ],
            'zoho_workplace' => [
                'page_key' => 'zoho_workplace',
                'page_type' => PageType::Product,
                'slug' => '/products/zoho-workplace',
                'is_active' => true,
                'seo_title' => 'Zoho Workplace for Modern Team Collaboration | INFX Solutions',
                'meta_description' => 'Move email, chat, docs, intranet, and meetings into one privacy-first workspace with Zoho Workplace and INFX Solutions.',
                'og_title' => 'Zoho Workplace | INFX Solutions',
                'og_description' => 'A cleaner collaboration stack for teams ready to move beyond generic productivity suites.',
                'og_image_url' => null,
                'robots' => 'index, follow',
                'cta_text' => 'Scope My Workplace Migration',
                'cta_url' => '#consultation',
                'trial_url' => 'https://store.zoho.com/ResellerCustomerSignUp.do?id=2afc43fa6ca997e0dae84629788ad5e8',
                'body_config' => [
                    'nav_label' => 'Zoho Workplace',
                    'eyebrow' => 'Email, Chat, Docs, Meetings, and Intranet In One Workspace',
                    'headline' => 'Give your team one calmer workspace',
                    'subheadline' => 'Zoho Workplace brings email, documents, meetings, chat, and internal communication together in a privacy-first suite that is easier to adopt and easier to manage.',
                    'hero_badges' => [
                        'Qualify to unlock a free trial',
                        'Migration assistance available',
                        'Privacy-first platform',
                    ],
                    'hero_points' => [
                        'Professional email, chat, docs, meetings, and intranet in one suite',
                        'Strong fit for teams that care about privacy, simplicity, and cleaner collaboration habits',
                        'Qualify below to unlock a free trial and bring INFX in when you want migration support',
                    ],
                    'hero_metrics' => [
                        ['value' => '120', 'label' => 'Users migrated in a single weekend'],
                        ['value' => '1', 'label' => 'Workspace instead of scattered tools'],
                        ['value' => '24h', 'label' => 'Average reply time'],
                    ],
                    'hero_logo_block' => [
                        'label' => 'A better fit for privacy-first collaboration',
                        'items' => [
                            [
                                'src' => '/brand/zoho-workplace.svg',
                                'alt' => 'Zoho Workplace',
                                'title' => 'Zoho Workplace',
                                'description' => 'A calmer collaboration suite for email, documents, meetings, and team communication.',
                            ],
                        ],
                    ],
                    'trust_heading' => 'Why teams choose Zoho Workplace.',
                    'trust_copy' => 'It brings email, files, chat, meetings, and intranet tools together in one calmer workspace that is easier to manage.',
                    'trust_blocks' => [
                        [
                            'icon' => 'mail',
                            'title' => 'Unified communication stack',
                            'description' => 'Bring mail, meetings, chat, and file collaboration into one place the team can actually use daily.',
                        ],
                        [
                            'icon' => 'document',
                            'title' => 'Knowledge sharing without chaos',
                            'description' => 'Give the team shared documents, intranet updates, and searchable information in one workspace.',
                        ],
                        [
                            'icon' => 'shield',
                            'title' => 'Privacy-first migration path',
                            'description' => 'Move away from the current suite without losing the structure people rely on every day.',
                        ],
                    ],
                    'spotlight' => [
                        'media_type' => 'video',
                        'media_url' => 'media/zoho-workplace-workdrive.mp4',
                        'media_alt' => 'Zoho Workplace collaboration video',
                    ],
                    'offer_title' => 'What Zoho Workplace unlocks',
                    'offer_copy' => 'A strong fit for businesses that want cleaner internal collaboration, simpler administration, and a privacy-first alternative to mainstream suites. Qualify below to unlock a free trial and see how the suite fits your team\'s daily work.',
                    'highlights' => [
                        'Custom-domain business email',
                        'Channels, chat, and team communication',
                        'Document collaboration and file sharing',
                        'Internal communication through a company intranet',
                    ],
                    'logo_bank' => [
                        'title' => 'A calmer collaboration stack is easier to picture with real product motion.',
                        'copy' => 'These previews show the kind of workplace experience teams move into when email, docs, chat, and meetings stop living in separate silos.',
                        'items' => [
                            [
                                'src' => '/brand/zoho-workplace.svg',
                                'alt' => 'Zoho Workplace',
                                'title' => 'File work without the clutter',
                                'description' => 'Keep shared documents and daily collaboration in a simpler workspace that is easier for teams to adopt.',
                                'media_type' => 'video',
                                'media_url' => 'media/zoho-workplace-workdrive.mp4',
                                'media_alt' => 'Zoho Workplace WorkDrive preview',
                            ],
                            [
                                'src' => '/brand/zoho-workplace.svg',
                                'alt' => 'Zoho Workplace',
                                'title' => 'A workspace people actually enjoy using',
                                'description' => 'Bring communication and teamwork into one calmer daily rhythm instead of scattered platform habits.',
                                'media_type' => 'video',
                                'media_url' => 'media/zoho-workplace-happiness.mp4',
                                'media_alt' => 'Zoho Workplace collaboration preview',
                            ],
                            [
                                'src' => '/brand/zoho-workplace.svg',
                                'alt' => 'Zoho Workplace',
                                'title' => 'Flexibility for hybrid teams',
                                'description' => 'Support remote and in-office work without adding more platform noise or admin overhead.',
                                'media_type' => 'video',
                                'media_url' => 'media/zoho-workplace-flexibility.mp4',
                                'media_alt' => 'Zoho Workplace flexibility preview',
                            ],
                        ],
                    ],
                    'outcome_heading' => 'What changes after the team moves into a cleaner workplace stack.',
                    'outcome_copy' => 'The real value is less fragmentation and less confusion across the tools people touch every day.',
                    'outcomes' => [
                        [
                            'metric' => '1',
                            'title' => 'Unified workplace',
                            'description' => 'Put communication, collaboration, and file access under one clearer daily stack.',
                        ],
                        [
                            'metric' => '↓',
                            'title' => 'Less disruption during migration',
                            'description' => 'Use phased migration options and partner support to reduce risk for the team.',
                        ],
                        [
                            'metric' => '↑',
                            'title' => 'Better collaboration',
                            'description' => 'Improve document access, internal communication, and cross-team work in one suite.',
                        ],
                        [
                            'metric' => '0',
                            'title' => 'Need for extra collaboration clutter',
                            'description' => 'Reduce the number of ad hoc tools used to patch over gaps in the current setup.',
                        ],
                    ],
                    'feature_heading' => 'Why teams move to Workplace',
                    'feature_subheading' => 'The real win is alignment: fewer disconnected habits, cleaner tooling, and better day-to-day collaboration.',
                    'features' => [
                        [
                            'icon' => 'mail',
                            'title' => 'Professional communication stack',
                            'description' => 'Unify email, chat, and meetings so communication no longer fragments across ad hoc tools.',
                        ],
                        [
                            'icon' => 'document',
                            'title' => 'Shared knowledge and documents',
                            'description' => 'Keep files, collaboration, and internal updates in one environment with less tool switching.',
                        ],
                        [
                            'icon' => 'shield',
                            'title' => 'Privacy-first posture',
                            'description' => 'Choose a suite that aligns with businesses prioritising data stewardship and practical governance.',
                        ],
                        [
                            'icon' => 'swap',
                            'title' => 'Guided migration support',
                            'description' => 'Move people, mail, calendars, and collaboration patterns with a structured migration plan.',
                        ],
                    ],
                    'faq_heading' => 'Questions teams ask before migrating email and collaboration to Zoho Workplace.',
                    'faq_copy' => 'Migration and adoption are the usual blockers, so the FAQ is designed to remove that uncertainty quickly.',
                    'faq' => [
                        [
                            'question' => 'Can you migrate us from Google Workspace or Microsoft 365?',
                            'answer' => 'Yes. We can scope staged migrations for mail, calendars, documents, and internal collaboration patterns.',
                        ],
                        [
                            'question' => 'Is Workplace only for small teams?',
                            'answer' => 'No. It works from small teams upward, especially when privacy, simplicity, and cost discipline matter.',
                        ],
                        [
                            'question' => 'Will our team struggle to adopt it?',
                            'answer' => 'We help with onboarding, training, and the move into day-to-day use so the change feels easier on the team.',
                        ],
                        [
                            'question' => 'Can we start with a free trial before committing to migration?',
                            'answer' => 'Yes. Complete the quick fit check and you can start a free trial first, then bring INFX in when you want help with migration planning, onboarding, or rollout.',
                        ],
                    ],
                    'cta_heading' => 'Thinking about trialling a cleaner collaboration stack for your team?',
                    'cta_copy' => 'Complete the quick fit check to unlock your free trial or book a migration consultation if you want help planning the move first.',
                ],
            ],
            'thanks' => [
                'page_key' => 'thanks',
                'page_type' => PageType::ThankYou,
                'slug' => '/thanks',
                'is_active' => true,
                'seo_title' => 'Thanks, We Have Your Request | INFX Solutions',
                'meta_description' => 'Your enquiry has been received and the INFX team will be in touch shortly.',
                'og_title' => 'Thanks | INFX Solutions',
                'og_description' => 'Your consultation request is in.',
                'og_image_url' => null,
                'robots' => 'noindex, nofollow',
                'cta_text' => 'Back to the Hub',
                'cta_url' => '/',
                'trial_url' => null,
                'body_config' => [
                    'headline' => 'You are all set.',
                    'subheadline' => 'Your request is in and a Zoho specialist will reach out within one business day with the next best step.',
                ],
            ],
            'privacy' => [
                'page_key' => 'privacy',
                'page_type' => PageType::Legal,
                'slug' => '/privacy',
                'is_active' => true,
                'seo_title' => 'Privacy Policy | INFX Solutions',
                'meta_description' => 'How INFX Solutions collects, uses, and protects personal information.',
                'og_title' => 'Privacy Policy | INFX Solutions',
                'og_description' => 'INFX Solutions privacy policy.',
                'og_image_url' => null,
                'robots' => 'index, follow',
                'cta_text' => null,
                'cta_url' => null,
                'trial_url' => null,
                'body_config' => [
                    'last_updated' => 'April 2026',
                ],
            ],
            'terms' => [
                'page_key' => 'terms',
                'page_type' => PageType::Legal,
                'slug' => '/terms',
                'is_active' => true,
                'seo_title' => 'Terms of Service | INFX Solutions',
                'meta_description' => 'Terms governing the use of the INFX Solutions website and service enquiries.',
                'og_title' => 'Terms of Service | INFX Solutions',
                'og_description' => 'INFX Solutions terms of service.',
                'og_image_url' => null,
                'robots' => 'index, follow',
                'cta_text' => null,
                'cta_url' => null,
                'trial_url' => null,
                'body_config' => [
                    'last_updated' => 'April 2026',
                ],
            ],
        ];
    }

    /**
     * @return PageCatalogDefinition
     */
    public static function definition(string $pageKey): array
    {
        $definition = self::definitions()[$pageKey] ?? null;

        if ($definition === null) {
            throw new InvalidArgumentException("Unknown page catalog key [{$pageKey}].");
        }

        return $definition;
    }

    /**
     * @return array<int, ProductNavigationItem>
     */
    public static function productNavigation(): array
    {
        return collect(self::definitions())
            ->filter(fn (array $definition): bool => $definition['page_type'] === PageType::Product)
            ->map(static function (array $definition): array {
                $navLabel = Arr::get($definition['body_config'], 'nav_label');

                return [
                    'page_key' => $definition['page_key'],
                    'label' => is_string($navLabel) && $navLabel !== '' ? $navLabel : $definition['page_key'],
                    'slug' => $definition['slug'],
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return PageCatalogDefinition
     */
    public static function resolve(string $pageKey, ?Page $page = null): array
    {
        $definition = self::definition($pageKey);
        $record = $page;

        if ($record === null) {
            /** @var Page|null $record */
            $record = Page::query()->where('page_key', $pageKey)->first();
        }

        if ($record !== null && ! $record->is_active) {
            throw new InvalidArgumentException("Inactive page [{$pageKey}] cannot be resolved.");
        }

        $resolved = array_merge($definition, array_filter([
            'slug' => $record?->slug,
            'seo_title' => $record?->seo_title,
            'meta_description' => $record?->meta_description,
            'og_title' => $record?->og_title,
            'og_description' => $record?->og_description,
            'og_image_url' => $record?->og_image_url,
            'robots' => $record?->robots,
            'cta_text' => $record?->cta_text,
            'cta_url' => $record?->cta_url,
            'trial_url' => $record?->trial_url,
        ], static fn (mixed $value): bool => $value !== null));

        if ($record === null) {
            $resolved['page_type'] = $definition['page_type'];
            $resolved['is_active'] = $definition['is_active'];
            $resolved['body_config'] = $definition['body_config'];

            return $resolved;
        }

        $resolved['page_type'] = $record->page_type;
        $resolved['is_active'] = $record->is_active;
        $resolved['body_config'] = array_replace_recursive(
            $definition['body_config'],
            $record->body_config ?? [],
        );

        return $resolved;
    }

    public static function pageKeyForSlug(string $slug, ?PageType $pageType = null): ?string
    {
        foreach (self::definitions() as $pageKey => $definition) {
            if ($definition['slug'] !== $slug) {
                continue;
            }

            if ($pageType !== null && $definition['page_type'] !== $pageType) {
                continue;
            }

            return $pageKey;
        }

        return null;
    }

    public static function productRouteSegment(string $pageKey): string
    {
        $definition = self::definition($pageKey);
        $slug = $definition['slug'];

        return Str::afterLast($slug, '/');
    }
}
