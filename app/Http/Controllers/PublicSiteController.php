<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\PageType;
use App\Models\Page;
use App\Support\PageCatalog;
use Illuminate\Contracts\View\View;

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
        } catch (\InvalidArgumentException) {
            abort(404);
        }
    }
}
