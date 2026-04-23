<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Support\PageCatalog;
use Illuminate\Http\JsonResponse;

class PageConfigController extends Controller
{
    public function show(string $pageKey): JsonResponse
    {
        /** @var Page|null $page */
        $page = Page::query()
            ->where('page_key', $pageKey)
            ->where('is_active', true)
            ->first();

        if ($page === null) {
            return response()->json(['message' => 'Page not found.'], 404);
        }

        $resolvedPage = PageCatalog::resolve($pageKey, $page);

        return response()->json([
            'page_key' => $resolvedPage['page_key'],
            'page_type' => $page->page_type->value,
            'slug' => $resolvedPage['slug'],
            'seo_title' => $resolvedPage['seo_title'],
            'meta_description' => $resolvedPage['meta_description'],
            'og_title' => $resolvedPage['og_title'],
            'og_description' => $resolvedPage['og_description'],
            'og_image_url' => $resolvedPage['og_image_url'],
            'robots' => $resolvedPage['robots'],
            'cta_text' => $resolvedPage['cta_text'],
            'cta_url' => $resolvedPage['cta_url'],
            'trial_url' => $resolvedPage['trial_url'],
            'body_config' => $resolvedPage['body_config'],
        ]);
    }
}
