<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\PageType;
use App\Models\Page;
use App\Support\PageCatalog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use InvalidArgumentException;

class PublicSiteController extends Controller
{
    public function landing(): View
    {
        return view('pages.landing', [
            'page' => $this->resolvePageByKey('landing', PageType::Landing),
        ]);
    }

    public function product(string $slug): View
    {
        $pageKey = PageCatalog::pageKeyForSlug("/products/{$slug}", PageType::Product);

        abort_if($pageKey === null, 404);

        return view('pages.products.show', [
            'page' => $this->resolvePageByKey($pageKey, PageType::Product),
        ]);
    }

    public function thanks(): View
    {
        return view('pages.thanks', [
            'page' => $this->resolvePageByKey('thanks', PageType::ThankYou),
        ]);
    }

    public function privacy(): View
    {
        return view('pages.privacy', [
            'page' => $this->resolvePageByKey('privacy', PageType::Legal),
        ]);
    }

    public function terms(): View
    {
        return view('pages.terms', [
            'page' => $this->resolvePageByKey('terms', PageType::Legal),
        ]);
    }

    public function robots(): Response
    {
        return response(implode(PHP_EOL, $this->robotsLines()), 200)
            ->header('Content-Type', 'text/plain; charset=UTF-8');
    }

    public function sitemap(): Response
    {
        return response()
            ->view('sitemap', ['urls' => $this->sitemapUrls()], 200)
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    /**
     * @return array<string, mixed>
     */
    private function resolvePageByKey(string $pageKey, PageType $expectedType): array
    {
        /** @var Page|null $record */
        $record = Page::query()->where('page_key', $pageKey)->first();

        if ($record !== null) {
            abort_if(! $record->is_active, 404);
            abort_if($record->page_type !== $expectedType, 404);
        }

        try {
            return PageCatalog::resolve($pageKey, $record);
        } catch (InvalidArgumentException) {
            abort(404);
        }
    }

    /**
     * @return list<array{loc: string}>
     */
    private function sitemapUrls(): array
    {
        /** @var array<string, Page> $recordsByKey */
        $recordsByKey = Page::query()->get()->keyBy('page_key')->all();
        $urls = [];

        foreach (array_keys(PageCatalog::definitions()) as $pageKey) {
            $record = $recordsByKey[$pageKey] ?? null;

            try {
                $page = PageCatalog::resolve($pageKey, $record);
            } catch (InvalidArgumentException) {
                continue;
            }

            $robots = strtolower($page['robots']);
            $isIndexable = str_contains($robots, 'index') && ! str_contains($robots, 'noindex');

            if (! $isIndexable) {
                continue;
            }

            $urls[] = ['loc' => url((string) $page['slug'])];
        }

        return $urls;
    }

    /**
     * @return list<string>
     */
    private function robotsLines(): array
    {
        return [
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
            'Sitemap: '.route('sitemap'),
        ];
    }
}
