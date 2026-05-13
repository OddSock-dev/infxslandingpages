<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\PageType;
use App\Models\Journey;
use App\Models\TrialClick;
use App\Support\PageCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class TrialStartController extends Controller
{
    public function __invoke(Request $request, string $pageKey): RedirectResponse
    {
        try {
            $page = PageCatalog::resolve($pageKey);
        } catch (InvalidArgumentException) {
            abort(404);
        }

        $trialUrl = data_get($page, 'trial_url');

        abort_unless($page['page_type'] === PageType::Product, 404);
        abort_unless(is_string($trialUrl) && trim($trialUrl) !== '', 404);

        $journey = $this->resolveJourney($request->string('journey')->toString(), $pageKey);
        $sessionId = $request->session()->getId();
        $sourcePageKey = $journey instanceof Journey ? $journey->source_page_key : $pageKey;
        $journeyReferrer = $journey instanceof Journey ? $journey->referrer : null;
        $utmSource = $journey instanceof Journey ? $journey->utm_source : $this->queryStringOrNull($request, 'utm_source');
        $utmMedium = $journey instanceof Journey ? $journey->utm_medium : $this->queryStringOrNull($request, 'utm_medium');
        $utmCampaign = $journey instanceof Journey ? $journey->utm_campaign : $this->queryStringOrNull($request, 'utm_campaign');
        $utmTerm = $journey instanceof Journey ? $journey->utm_term : $this->queryStringOrNull($request, 'utm_term');
        $utmContent = $journey instanceof Journey ? $journey->utm_content : $this->queryStringOrNull($request, 'utm_content');

        TrialClick::query()->firstOrCreate(
            ['click_fingerprint' => $this->clickFingerprint($pageKey, $sessionId, $journey)],
            [
                'journey_id' => $journey?->id,
                'session_id' => $sessionId !== '' ? $sessionId : null,
                'page_key' => $pageKey,
                'product_key' => $pageKey,
                'source_page_key' => $sourcePageKey,
                'target_url' => trim($trialUrl),
                'meta_json' => array_filter([
                    'journey_referrer' => $journeyReferrer,
                    'referrer' => $request->headers->get('referer'),
                    'utm_source' => $utmSource,
                    'utm_medium' => $utmMedium,
                    'utm_campaign' => $utmCampaign,
                    'utm_term' => $utmTerm,
                    'utm_content' => $utmContent,
                    'ip_hash' => $request->ip() !== null ? hash('sha256', $request->ip()) : null,
                    'user_agent' => $request->userAgent(),
                ], static fn (mixed $value): bool => $value !== null && $value !== ''),
                'clicked_at' => now(),
            ],
        );

        return redirect()->away(trim($trialUrl));
    }

    private function resolveJourney(string $journeyToken, string $pageKey): ?Journey
    {
        if ($journeyToken === '') {
            return null;
        }

        /** @var Journey|null $journey */
        $journey = Journey::query()
            ->where('journey_token', $journeyToken)
            ->notExpired()
            ->first();

        abort_if($journey === null || $journey->assigned_product_key !== $pageKey, 404);

        return $journey;
    }

    private function clickFingerprint(string $pageKey, string $sessionId, ?Journey $journey): string
    {
        $identity = $journey instanceof Journey
            ? $journey->journey_token
            : ($sessionId !== '' ? $sessionId : 'no-session');

        return hash('sha256', implode('|', [
            $pageKey,
            $identity,
        ]));
    }

    private function queryStringOrNull(Request $request, string $key): ?string
    {
        $value = $request->string($key)->toString();

        return $value !== '' ? $value : null;
    }
}
