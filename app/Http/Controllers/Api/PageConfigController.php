<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Page;
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

        return response()->json([
            'page_key' => $page->page_key,
            'page_type' => $page->page_type->value,
            'slug' => $page->slug,
            'seo_title' => $page->seo_title,
            'meta_description' => $page->meta_description,
            'og_title' => $page->og_title,
            'og_description' => $page->og_description,
            'og_image_url' => $page->og_image_url,
            'robots' => $page->robots,
            'cta_text' => $page->cta_text,
            'cta_url' => $page->cta_url,
            'trial_url' => $page->trial_url,
            'body_config' => $page->body_config,
        ]);
    }
}
