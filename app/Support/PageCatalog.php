<?php

declare(strict_types=1);

namespace App\Support;

use App\Enums\PageType;
use App\Models\Page;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use InvalidArgumentException;

class PageCatalog
{
    /**
     * @return array<string, array<string, mixed>>
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
                'meta_description' => 'High-converting Zoho landing pages, local South African rollout support, and a guided matching flow that gets teams to the right Zoho stack fast.',
                'og_title' => 'INFX Solutions | Zoho Growth Partner',
                'og_description' => 'Match your business to the right Zoho solution in under two minutes, then launch with a local implementation team.',
                'og_image_url' => null,
                'robots' => 'index, follow',
                'cta_text' => 'Find My Zoho Stack',
                'cta_url' => '#qualify',
                'trial_url' => null,
                'body_config' => [
                    'eyebrow' => 'South Africa\'s Zoho Growth Partner',
                    'headline' => 'Find the Zoho stack that actually fits the way your team sells, markets, and operates.',
                    'subheadline' => 'Use a guided matching flow built for real buying journeys, then hand implementation to a local partner that knows how to turn interest into adoption.',
                    'hero_points' => [
                        '45+ Zoho apps mapped into one connected operating system',
                        'South African onboarding, implementation, and optimisation support',
                        'POPIA-conscious journeys with CRM-ready lead handoff',
                    ],
                    'hero_metrics' => [
                        ['value' => '200+', 'label' => 'South African teams launched'],
                        ['value' => '45+', 'label' => 'Zoho apps across the platform'],
                        ['value' => '7+', 'label' => 'Years of Zoho delivery experience'],
                        ['value' => '98%', 'label' => 'Client satisfaction score'],
                    ],
                    'trust_marks' => [
                        'Zoho Authorised Partner',
                        'SA-Based Delivery Team',
                        'POPIA-Aligned Lead Capture',
                    ],
                    'journey_steps' => [
                        [
                            'title' => 'Tell us what the business needs',
                            'description' => 'Choose the closest match and we route you to the best-fit Zoho product journey.',
                        ],
                        [
                            'title' => 'Capture the right buying context',
                            'description' => 'We retain the qualification path so sales follow-up starts with context instead of guesswork.',
                        ],
                        [
                            'title' => 'Convert interest into implementation',
                            'description' => 'Your team gets a focused consultation, rollout plan, and a partner who stays involved after the form fill.',
                        ],
                    ],
                    'feature_heading' => 'Why high-intent buyers convert better with INFX',
                    'feature_subheading' => 'The message, routing, and handoff are designed to feel premium without becoming complicated.',
                    'features' => [
                        [
                            'icon' => 'spark',
                            'title' => 'Message-to-product fit',
                            'description' => 'Each journey sharpens the promise so visitors do not have to interpret a generic all-in-one software pitch.',
                        ],
                        [
                            'icon' => 'compass',
                            'title' => 'Faster buying confidence',
                            'description' => 'Qualification creates momentum by reducing uncertainty before the contact form ever appears.',
                        ],
                        [
                            'icon' => 'shield',
                            'title' => 'Clean downstream handoff',
                            'description' => 'Every lead is saved with route context, UTM data, and CRM-ready metadata for the follow-up team.',
                        ],
                        [
                            'icon' => 'bolt',
                            'title' => 'Local implementation edge',
                            'description' => 'Strategy, setup, migration, and change management come from a South African team that knows the terrain.',
                        ],
                    ],
                    'testimonials' => [
                        [
                            'quote' => 'INFX helped us move from “we should probably look at Zoho” to a confident rollout plan in one conversation.',
                            'author' => 'Alicia M.',
                            'role' => 'Operations Lead, Cape Town',
                        ],
                        [
                            'quote' => 'The qualification flow felt premium and the follow-up was already personalised. We never had to repeat ourselves.',
                            'author' => 'Gareth N.',
                            'role' => 'Managing Director, Johannesburg',
                        ],
                        [
                            'quote' => 'They understood the commercial side and the implementation side. That combination is rare.',
                            'author' => 'Lerato P.',
                            'role' => 'Revenue Manager, Pretoria',
                        ],
                    ],
                    'faq' => [
                        [
                            'question' => 'What does the matching flow actually do?',
                            'answer' => 'It routes the visitor to the most relevant Zoho product journey, stores the journey context, and ties that context to the final consultation request.',
                        ],
                        [
                            'question' => 'Do we have to commit to Zoho before talking to you?',
                            'answer' => 'No. The goal is to clarify fit first. The consultation is there to help you understand whether Zoho One, Marketing Plus, Workplace, or another path makes the most sense.',
                        ],
                        [
                            'question' => 'Can you migrate us from our current tools?',
                            'answer' => 'Yes. We regularly help teams move from fragmented stacks, legacy CRMs, Google Workspace, Microsoft 365, and other marketing or collaboration tools.',
                        ],
                        [
                            'question' => 'Is this suited to South African compliance requirements?',
                            'answer' => 'Yes. We design journeys and follow-up processes with POPIA-conscious data handling, practical governance, and regional delivery considerations in mind.',
                        ],
                    ],
                    'cta_heading' => 'Ready to stop guessing which Zoho product to back?',
                    'cta_copy' => 'Start with a guided match, then let us shape the rollout plan around your revenue motion, delivery model, and team size.',
                    'cta_primary' => [
                        'label' => 'Start the Match',
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
                'og_description' => 'A single operating system for your entire business, implemented for real-world adoption.',
                'og_image_url' => null,
                'robots' => 'index, follow',
                'cta_text' => 'Request a Zoho One Consultation',
                'cta_url' => '#consultation',
                'trial_url' => 'https://www.zoho.com/one/signup.html',
                'body_config' => [
                    'nav_label' => 'Zoho One',
                    'eyebrow' => 'One Operating System. 45+ Connected Apps.',
                    'headline' => 'Replace software sprawl with a connected operating system built for ambitious teams.',
                    'subheadline' => 'Zoho One brings sales, finance, operations, HR, marketing, and service into one ecosystem so your business can scale without stitching together disconnected tools.',
                    'hero_points' => [
                        'CRM, finance, projects, HR, and support connected by design',
                        'Fewer sync failures, fewer duplicate records, more operating clarity',
                        'Implementation support tuned for real internal adoption, not shelfware',
                    ],
                    'hero_metrics' => [
                        ['value' => '45+', 'label' => 'Integrated business apps'],
                        ['value' => '1', 'label' => 'Connected commercial system'],
                        ['value' => '24h', 'label' => 'Lead response target'],
                    ],
                    'offer_title' => 'What Zoho One unlocks',
                    'offer_copy' => 'Ideal for businesses that have outgrown tool-by-tool decision-making and now need one commercial backbone across the organisation.',
                    'highlights' => [
                        'Sales and CRM visibility',
                        'Project and service delivery tracking',
                        'Finance and invoicing workflows',
                        'Cross-team reporting and automation',
                    ],
                    'feature_heading' => 'Why buyers choose Zoho One',
                    'feature_subheading' => 'The value is not just the app count. It is the way those apps remove friction between teams.',
                    'features' => [
                        [
                            'icon' => 'layers',
                            'title' => 'Shared data foundation',
                            'description' => 'Information flows across the stack without the brittle patchwork of third-party syncs.',
                        ],
                        [
                            'icon' => 'chart',
                            'title' => 'Executive visibility',
                            'description' => 'See pipeline, delivery, support, and finance signals together instead of in separate dashboards.',
                        ],
                        [
                            'icon' => 'gear',
                            'title' => 'Process automation',
                            'description' => 'Automate approvals, routing, handoffs, and alerts across departments as the business matures.',
                        ],
                        [
                            'icon' => 'rocket',
                            'title' => 'Room to grow',
                            'description' => 'Start with the apps you need now and expand inside the same ecosystem as requirements evolve.',
                        ],
                    ],
                    'testimonials' => [
                        [
                            'quote' => 'Zoho One replaced a stack of loosely connected tools and finally gave leadership one version of the truth.',
                            'author' => 'David L.',
                            'role' => 'CFO, Johannesburg',
                        ],
                        [
                            'quote' => 'INFX made the rollout practical. We were not buying software in a vacuum; we were redesigning how the business ran.',
                            'author' => 'Anele M.',
                            'role' => 'COO, Cape Town',
                        ],
                    ],
                    'faq' => [
                        [
                            'question' => 'Is Zoho One only for large enterprises?',
                            'answer' => 'No. It is especially strong for growing businesses that want one scalable foundation instead of re-platforming every year.',
                        ],
                        [
                            'question' => 'Can we implement in phases?',
                            'answer' => 'Yes. We typically scope a phased rollout so the first wave creates operational lift quickly while later phases handle deeper process work.',
                        ],
                        [
                            'question' => 'Can you migrate our existing data?',
                            'answer' => 'Yes. We handle structured migrations from CRMs, spreadsheets, support tools, finance systems, and collaboration platforms.',
                        ],
                    ],
                    'cta_heading' => 'Want to see whether Zoho One can become your commercial backbone?',
                    'cta_copy' => 'Book a consultation and we will map your current stack, bottlenecks, and rollout priorities.',
                ],
            ],
            'zoho_marketing_plus' => [
                'page_key' => 'zoho_marketing_plus',
                'page_type' => PageType::Product,
                'slug' => '/products/zoho-marketing-plus',
                'is_active' => true,
                'seo_title' => 'Zoho Marketing Plus for Unified Growth Teams | INFX Solutions',
                'meta_description' => 'Bring campaigns, social, webinars, events, journeys, and attribution together with Zoho Marketing Plus and INFX Solutions.',
                'og_title' => 'Zoho Marketing Plus | INFX Solutions',
                'og_description' => 'A unified marketing platform for teams that need clarity, coordination, and conversion.',
                'og_image_url' => null,
                'robots' => 'index, follow',
                'cta_text' => 'Plan My Marketing Plus Rollout',
                'cta_url' => '#consultation',
                'trial_url' => 'https://www.zoho.com/marketingplus/signup.html',
                'body_config' => [
                    'nav_label' => 'Zoho Marketing Plus',
                    'eyebrow' => 'One Home For Campaigns, Journeys, Events, and Attribution',
                    'headline' => 'Turn fragmented campaign execution into one coordinated growth engine.',
                    'subheadline' => 'Zoho Marketing Plus helps your team plan, launch, measure, and refine campaigns from one platform instead of juggling disconnected tools.',
                    'hero_points' => [
                        'Email, social, webinars, events, and journeys under one roof',
                        'Sharper attribution and cleaner campaign coordination',
                        'Built for marketing teams that need speed without losing control',
                    ],
                    'hero_metrics' => [
                        ['value' => '40%', 'label' => 'Typical engagement lift after journey clean-up'],
                        ['value' => '1', 'label' => 'Unified campaign workspace'],
                        ['value' => '0', 'label' => 'Tolerance for tool sprawl'],
                    ],
                    'offer_title' => 'What Zoho Marketing Plus unlocks',
                    'offer_copy' => 'Best suited to teams that already have marketing motion, but need stronger orchestration, attribution, and campaign consistency.',
                    'highlights' => [
                        'Cross-channel campaign planning',
                        'Journey automation and nurture design',
                        'Webinar and event management',
                        'Attribution that informs better budget choices',
                    ],
                    'feature_heading' => 'Why growth teams move to Marketing Plus',
                    'feature_subheading' => 'It creates one coordinated operating model for the entire marketing team.',
                    'features' => [
                        [
                            'icon' => 'megaphone',
                            'title' => 'Cross-channel coordination',
                            'description' => 'Run email, social, events, and nurture activity from one strategic layer instead of isolated campaign islands.',
                        ],
                        [
                            'icon' => 'route',
                            'title' => 'Journey design that converts',
                            'description' => 'Build behaviour-based customer journeys with less manual stitching and clearer follow-up logic.',
                        ],
                        [
                            'icon' => 'pulse',
                            'title' => 'Attribution with context',
                            'description' => 'See which channels and campaigns are actually driving qualified demand and downstream revenue.',
                        ],
                        [
                            'icon' => 'spark',
                            'title' => 'Faster campaign execution',
                            'description' => 'Reduce switching costs between tools so the team can launch more confidently and iterate faster.',
                        ],
                    ],
                    'testimonials' => [
                        [
                            'quote' => 'We replaced four separate tools and our campaign team finally started working from the same operating rhythm.',
                            'author' => 'Leigh S.',
                            'role' => 'Head of Marketing, Durban',
                        ],
                        [
                            'quote' => 'The attribution clarity was the unlock. We stopped guessing which activity mattered.',
                            'author' => 'Thabo N.',
                            'role' => 'Digital Marketing Manager',
                        ],
                    ],
                    'faq' => [
                        [
                            'question' => 'How is Marketing Plus different from standalone email tools?',
                            'answer' => 'It gives you orchestration across multiple channels, not just email execution. That means better coordination, cleaner journeys, and stronger reporting.',
                        ],
                        [
                            'question' => 'Can it connect to Zoho CRM?',
                            'answer' => 'Yes. It works particularly well when paired with Zoho CRM or the wider Zoho ecosystem, but can also fit into mixed environments.',
                        ],
                        [
                            'question' => 'Is it right for smaller teams?',
                            'answer' => 'Yes, especially when the cost of switching between separate marketing tools is already slowing the team down.',
                        ],
                    ],
                    'cta_heading' => 'Ready to unify your campaigns and stop managing growth from five disconnected tools?',
                    'cta_copy' => 'Book a consultation and we will map the best-fit rollout for your demand generation engine.',
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
                'trial_url' => 'https://www.zoho.com/workplace/signup.html',
                'body_config' => [
                    'nav_label' => 'Zoho Workplace',
                    'eyebrow' => 'Email, Chat, Docs, Meetings, and Intranet In One Workspace',
                    'headline' => 'Give your team a modern collaboration layer without buying into more platform noise.',
                    'subheadline' => 'Zoho Workplace replaces scattered collaboration habits with one privacy-first environment for communication, content, and internal alignment.',
                    'hero_points' => [
                        'Professional email, chat, meetings, docs, and intranet in one suite',
                        'Strong fit for teams that care about privacy and platform simplicity',
                        'Migration support for Google Workspace and Microsoft 365 environments',
                    ],
                    'hero_metrics' => [
                        ['value' => '120', 'label' => 'Users migrated in a single weekend'],
                        ['value' => '1', 'label' => 'Workspace instead of scattered tools'],
                        ['value' => '24h', 'label' => 'Average turnaround for new consultation leads'],
                    ],
                    'offer_title' => 'What Zoho Workplace unlocks',
                    'offer_copy' => 'A strong fit for businesses that want cleaner internal collaboration, simpler administration, and a privacy-first alternative to mainstream suites.',
                    'highlights' => [
                        'Custom-domain business email',
                        'Channels, chat, and team communication',
                        'Document collaboration and file sharing',
                        'Internal communication through a company intranet',
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
                            'description' => 'Keep files, collaboration, and internal updates in one environment with less context switching.',
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
                    'testimonials' => [
                        [
                            'quote' => 'The migration from Google Workspace was smoother than we expected and the team adopted the new suite quickly.',
                            'author' => 'Brendan T.',
                            'role' => 'IT Manager, Pretoria',
                        ],
                        [
                            'quote' => 'We cut costs, improved privacy posture, and gave the team a calmer collaboration setup.',
                            'author' => 'Nomsa D.',
                            'role' => 'CTO, Fintech Startup',
                        ],
                    ],
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
                            'answer' => 'Adoption is part of the rollout. We help plan training, change communication, and the handover into day-to-day use.',
                        ],
                    ],
                    'cta_heading' => 'Thinking about a cleaner collaboration stack for your team?',
                    'cta_copy' => 'Book a migration consultation and we will map the move, risks, and the right rollout path.',
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
     * @return array<string, mixed>
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
     * @return array<int, array{page_key: string, label: string, slug: string}>
     */
    public static function productNavigation(): array
    {
        return collect(self::definitions())
            ->filter(fn (array $definition): bool => $definition['page_type'] === PageType::Product)
            ->map(fn (array $definition): array => [
                'page_key' => $definition['page_key'],
                'label' => (string) Arr::get($definition, 'body_config.nav_label', $definition['page_key']),
                'slug' => $definition['slug'],
            ])->values()->all();
    }

    /**
     * @return array<string, mixed>
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

        $resolved['page_type'] = $record?->page_type ?? $definition['page_type'];
        $resolved['is_active'] = $record?->is_active ?? $definition['is_active'];
        $resolved['body_config'] = array_replace_recursive(
            $definition['body_config'] ?? [],
            $record?->body_config ?? [],
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
        $slug = (string) self::definition($pageKey)['slug'];

        return Str::afterLast($slug, '/');
    }
}
