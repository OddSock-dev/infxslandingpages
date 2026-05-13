<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\SyncAttemptStatus;
use App\Models\CrmSyncAttempt;
use App\Models\Journey;
use App\Models\Submission;
use App\Models\TrialClick;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class DashboardMetricsService
{
    private const int DEFAULT_WINDOW_DAYS = 30;

    private const int TREND_WINDOW_DAYS = 7;

    /**
     * @return array{
     *     journeys_started: int,
     *     recommendations_issued: int,
     *     journey_consultations: int,
     *     trial_starts: int,
     *     stalled_handoffs: int,
     *     routed_rate: float,
     *     submission_rate: float,
     *     drop_off_rate: float,
     *     journey_trend: list<int>,
     *     recommendation_trend: list<int>,
     *     consultation_trend: list<int>,
     *     trial_start_trend: list<int>,
     *     stalled_trend: list<int>
     * }
     */
    public function funnelOverview(int $days = self::DEFAULT_WINDOW_DAYS): array
    {
        $windowStart = $this->windowStart($days);
        $trendStart = $this->windowStart(self::TREND_WINDOW_DAYS);
        $today = CarbonImmutable::now();

        $journeysStarted = Journey::query()
            ->where('created_at', '>=', $windowStart)
            ->count();

        $recommendationsIssued = Journey::query()
            ->where('created_at', '>=', $windowStart)
            ->whereNotNull('assigned_product_key')
            ->count();

        $journeyConsultations = Submission::query()
            ->where('submitted_at', '>=', $windowStart)
            ->whereNotNull('journey_id')
            ->count();

        $trialStarts = TrialClick::query()
            ->where('clicked_at', '>=', $windowStart)
            ->count();

        $stalledHandoffs = Journey::query()
            ->where('created_at', '>=', $windowStart)
            ->whereNotNull('assigned_product_key')
            ->whereDoesntHave('submission')
            ->where('expires_at', '<', $today)
            ->count();

        $trendDates = $this->trendDates();

        return [
            'journeys_started' => $journeysStarted,
            'recommendations_issued' => $recommendationsIssued,
            'journey_consultations' => $journeyConsultations,
            'trial_starts' => $trialStarts,
            'stalled_handoffs' => $stalledHandoffs,
            'routed_rate' => $this->percentage($recommendationsIssued, $journeysStarted),
            'submission_rate' => $this->percentage($journeyConsultations, $journeysStarted),
            'drop_off_rate' => $this->percentage($stalledHandoffs, $recommendationsIssued),
            'journey_trend' => $this->dailyTrend(
                $this->keyedIntegerMetrics(
                    Journey::query()
                        ->where('created_at', '>=', $trendStart)
                        ->selectRaw('DATE(created_at) as trend_date, COUNT(*) as aggregate_total')
                        ->groupBy('trend_date')
                        ->pluck('aggregate_total', 'trend_date'),
                ),
                $trendDates,
            ),
            'recommendation_trend' => $this->dailyTrend(
                $this->keyedIntegerMetrics(
                    Journey::query()
                        ->where('created_at', '>=', $trendStart)
                        ->whereNotNull('assigned_product_key')
                        ->selectRaw('DATE(created_at) as trend_date, COUNT(*) as aggregate_total')
                        ->groupBy('trend_date')
                        ->pluck('aggregate_total', 'trend_date'),
                ),
                $trendDates,
            ),
            'consultation_trend' => $this->dailyTrend(
                $this->keyedIntegerMetrics(
                    Submission::query()
                        ->where('submitted_at', '>=', $trendStart)
                        ->whereNotNull('journey_id')
                        ->selectRaw('DATE(submitted_at) as trend_date, COUNT(*) as aggregate_total')
                        ->groupBy('trend_date')
                        ->pluck('aggregate_total', 'trend_date'),
                ),
                $trendDates,
            ),
            'trial_start_trend' => $this->dailyTrend(
                $this->keyedIntegerMetrics(
                    TrialClick::query()
                        ->where('clicked_at', '>=', $trendStart)
                        ->selectRaw('DATE(clicked_at) as trend_date, COUNT(*) as aggregate_total')
                        ->groupBy('trend_date')
                        ->pluck('aggregate_total', 'trend_date'),
                ),
                $trendDates,
            ),
            'stalled_trend' => $this->dailyTrend(
                $this->keyedIntegerMetrics(
                    Journey::query()
                        ->whereNotNull('assigned_product_key')
                        ->whereDoesntHave('submission')
                        ->where('expires_at', '>=', $trendStart)
                        ->where('expires_at', '<', $today)
                        ->selectRaw('DATE(expires_at) as trend_date, COUNT(*) as aggregate_total')
                        ->groupBy('trend_date')
                        ->pluck('aggregate_total', 'trend_date'),
                ),
                $trendDates,
            ),
        ];
    }

    /**
     * @return array{
     *     labels: list<string>,
     *     journey_starts: list<int>,
     *     consultations: list<int>,
     *     trial_starts: list<int>
     * }
     */
    public function pageConversion(int $days = self::DEFAULT_WINDOW_DAYS): array
    {
        $windowStart = $this->windowStart($days);
        $effectivePageExpression = 'COALESCE(journeys.source_page_key, submissions.page_key)';

        /** @var array<string, int> $journeyStarts */
        $journeyStarts = $this->keyedIntegerMetrics(
            Journey::query()
                ->where('created_at', '>=', $windowStart)
                ->selectRaw('source_page_key, COUNT(*) as aggregate_total')
                ->groupBy('source_page_key')
                ->pluck('aggregate_total', 'source_page_key'),
        );

        /** @var array<string, int> $consultations */
        $consultations = $this->keyedIntegerMetrics(
            Submission::query()
                ->leftJoin('journeys', 'journeys.id', '=', 'submissions.journey_id')
                ->where('submissions.submitted_at', '>=', $windowStart)
                ->selectRaw("{$effectivePageExpression} as page_key, COUNT(*) as aggregate_total")
                ->groupByRaw($effectivePageExpression)
                ->pluck('aggregate_total', 'page_key'),
        );

        /** @var array<string, int> $trialStarts */
        $trialStarts = $this->keyedIntegerMetrics(
            TrialClick::query()
                ->where('clicked_at', '>=', $windowStart)
                ->selectRaw('source_page_key, COUNT(*) as aggregate_total')
                ->groupBy('source_page_key')
                ->pluck('aggregate_total', 'source_page_key'),
        );

        /** @var Collection<int, string> $pageKeys */
        $pageKeys = collect(array_unique([
            ...array_keys($journeyStarts),
            ...array_keys($consultations),
            ...array_keys($trialStarts),
        ]))
            ->sortBy(fn (string $pageKey): int => $this->pageSortOrder($pageKey))
            ->values();

        return [
            'labels' => array_values($pageKeys
                ->map(fn (string $pageKey): string => $this->friendlyLabel($pageKey))
                ->values()
                ->all()),
            'journey_starts' => array_values($pageKeys
                ->map(fn (string $pageKey): int => $journeyStarts[$pageKey] ?? 0)
                ->values()
                ->all()),
            'consultations' => array_values($pageKeys
                ->map(fn (string $pageKey): int => $consultations[$pageKey] ?? 0)
                ->values()
                ->all()),
            'trial_starts' => array_values($pageKeys
                ->map(fn (string $pageKey): int => $trialStarts[$pageKey] ?? 0)
                ->values()
                ->all()),
        ];
    }

    /**
     * @return array{
     *     labels: list<string>,
     *     open: list<int>,
     *     stalled: list<int>,
     *     submitted: list<int>
     * }
     */
    public function qualificationDropOff(int $days = self::DEFAULT_WINDOW_DAYS): array
    {
        $windowStart = $this->windowStart($days);
        $today = CarbonImmutable::now();

        /** @var Collection<int, object{product_key: string|null, open_total: int|string, stalled_total: int|string, submitted_total: int|string}> $rows */
        $rows = Journey::query()
            ->leftJoin('submissions', 'submissions.journey_id', '=', 'journeys.id')
            ->where('journeys.created_at', '>=', $windowStart)
            ->whereNotNull('journeys.assigned_product_key')
            ->selectRaw(
                'journeys.assigned_product_key as product_key,
                SUM(CASE WHEN submissions.id IS NULL AND journeys.expires_at >= ? THEN 1 ELSE 0 END) as open_total,
                SUM(CASE WHEN submissions.id IS NULL AND journeys.expires_at < ? THEN 1 ELSE 0 END) as stalled_total,
                SUM(CASE WHEN submissions.id IS NOT NULL THEN 1 ELSE 0 END) as submitted_total',
                [$today, $today],
            )
            ->groupBy('journeys.assigned_product_key')
            ->get();

        /** @var Collection<int, object{product_key: string|null, open_total: int|string, stalled_total: int|string, submitted_total: int|string}> $sortedRows */
        $sortedRows = $rows->sortBy(fn (object $row): int => $this->pageSortOrder((string) ($row->product_key ?? '')));

        return [
            'labels' => array_values($sortedRows
                ->map(fn (object $row): string => $this->friendlyLabel((string) $row->product_key))
                ->values()
                ->all()),
            'open' => array_values($sortedRows
                ->map(fn (object $row): int => (int) $row->open_total)
                ->values()
                ->all()),
            'stalled' => array_values($sortedRows
                ->map(fn (object $row): int => (int) $row->stalled_total)
                ->values()
                ->all()),
            'submitted' => array_values($sortedRows
                ->map(fn (object $row): int => (int) $row->submitted_total)
                ->values()
                ->all()),
        ];
    }

    /**
     * @return array{
     *     attributed_leads: int,
     *     unattributed_leads: int,
     *     total_submissions: int,
     *     top_source: string,
     *     top_source_count: int,
     *     top_campaign: string,
     *     top_campaign_count: int,
     *     top_entry_page: string,
     *     top_entry_page_count: int
     * }
     */
    public function attributionOverview(int $days = self::DEFAULT_WINDOW_DAYS): array
    {
        $windowStart = $this->windowStart($days);

        /** @var Collection<int, Submission> $submissions */
        $submissions = Submission::query()
            ->where('submitted_at', '>=', $windowStart)
            ->get(['page_key', 'meta_json']);

        $attributedLeads = 0;

        /** @var array<string, int> $sourceCounts */
        $sourceCounts = [];

        /** @var array<string, int> $campaignCounts */
        $campaignCounts = [];

        /** @var array<string, int> $pageCounts */
        $pageCounts = [];

        foreach ($submissions as $submission) {
            $meta = $submission->meta_json;

            $source = $this->metricKey($meta['utm_source'] ?? null, 'direct / unknown');
            $campaign = $this->metricKey($meta['utm_campaign'] ?? null, 'unlabelled campaign');
            $entryPage = $this->metricKey($meta['source_page_key'] ?? $submission->page_key, $submission->page_key);

            if (
                $this->hasSignal($meta['utm_source'] ?? null)
                || $this->hasSignal($meta['utm_campaign'] ?? null)
                || $this->hasSignal($meta['journey_referrer'] ?? null)
                || $this->hasSignal($meta['referrer'] ?? null)
            ) {
                $attributedLeads++;
            }

            $this->incrementMetric($sourceCounts, $source);
            $this->incrementMetric($campaignCounts, $campaign);
            $this->incrementMetric($pageCounts, $entryPage);
        }

        $topSource = $this->topMetric($sourceCounts, 'direct / unknown');
        $topCampaign = $this->topMetric($campaignCounts, 'unlabelled campaign');
        $topEntryPage = $this->topMetric($pageCounts, 'landing');

        return [
            'attributed_leads' => $attributedLeads,
            'unattributed_leads' => max($submissions->count() - $attributedLeads, 0),
            'total_submissions' => $submissions->count(),
            'top_source' => $this->friendlyLabel($topSource['key']),
            'top_source_count' => $topSource['count'],
            'top_campaign' => $this->friendlyLabel($topCampaign['key']),
            'top_campaign_count' => $topCampaign['count'],
            'top_entry_page' => $this->friendlyLabel($topEntryPage['key']),
            'top_entry_page_count' => $topEntryPage['count'],
        ];
    }

    /**
     * @return array{
     *     high_intent: int,
     *     warm_pipeline: int,
     *     nurture_needed: int,
     *     top_high_intent_product: string,
     *     top_high_intent_product_count: int
     * }
     */
    public function leadQualityOverview(int $days = self::DEFAULT_WINDOW_DAYS): array
    {
        $windowStart = $this->windowStart($days);

        /** @var Collection<int, Submission> $submissions */
        $submissions = Submission::query()
            ->where('submitted_at', '>=', $windowStart)
            ->get(['product_key', 'meta_json']);

        $highIntent = 0;
        $warmPipeline = 0;
        $nurtureNeeded = 0;

        /** @var array<string, int> $highIntentProducts */
        $highIntentProducts = [];

        foreach ($submissions as $submission) {
            $segment = $this->leadQualitySegment($submission->meta_json);

            match ($segment) {
                'high' => $highIntent++,
                'warm' => $warmPipeline++,
                default => $nurtureNeeded++,
            };

            if ($segment === 'high') {
                $this->incrementMetric($highIntentProducts, $submission->product_key);
            }
        }

        $topProduct = $this->topMetric($highIntentProducts, 'none');

        return [
            'high_intent' => $highIntent,
            'warm_pipeline' => $warmPipeline,
            'nurture_needed' => $nurtureNeeded,
            'top_high_intent_product' => $topProduct['count'] > 0 ? $this->friendlyLabel($topProduct['key']) : 'No high-intent trend yet',
            'top_high_intent_product_count' => $topProduct['count'],
        ];
    }

    /**
     * @return array{
     *     failed_last_day: int,
     *     pending_submissions: int,
     *     success_rate_last_7d: float,
     *     completed_attempts_last_7d: int,
     *     successful_attempts_last_7d: int,
     *     last_success_at: CarbonImmutable|null
     * }
     */
    public function syncHealth(): array
    {
        $today = CarbonImmutable::now();
        $weekStart = $today->subDays(7);

        $failedLastDay = CrmSyncAttempt::query()
            ->where('status', SyncAttemptStatus::Failed)
            ->where('attempted_at', '>=', $today->subDay())
            ->count();

        $successfulAttemptsLastWeek = CrmSyncAttempt::query()
            ->where('status', SyncAttemptStatus::Success)
            ->where('attempted_at', '>=', $weekStart)
            ->count();

        $completedAttemptsLastWeek = CrmSyncAttempt::query()
            ->whereIn('status', [SyncAttemptStatus::Success, SyncAttemptStatus::Failed])
            ->where('attempted_at', '>=', $weekStart)
            ->count();

        /** @var CrmSyncAttempt|null $lastSuccessfulAttempt */
        $lastSuccessfulAttempt = CrmSyncAttempt::query()
            ->where('status', SyncAttemptStatus::Success)
            ->latest('attempted_at')
            ->first(['attempted_at']);

        return [
            'failed_last_day' => $failedLastDay,
            'pending_submissions' => Submission::query()->pendingSync()->count(),
            'success_rate_last_7d' => $this->percentage($successfulAttemptsLastWeek, $completedAttemptsLastWeek),
            'completed_attempts_last_7d' => $completedAttemptsLastWeek,
            'successful_attempts_last_7d' => $successfulAttemptsLastWeek,
            'last_success_at' => $lastSuccessfulAttempt?->attempted_at,
        ];
    }

    private function windowStart(int $days): CarbonImmutable
    {
        return CarbonImmutable::now()->subDays($days)->startOfDay();
    }

    /** @return Collection<int, CarbonImmutable> */
    private function trendDates(): Collection
    {
        return collect(range(self::TREND_WINDOW_DAYS - 1, 0))
            ->map(fn (int $offset): CarbonImmutable => CarbonImmutable::now()->subDays($offset)->startOfDay());
    }

    /**
     * @param  array<string, int>  $dailyTotals
     * @param  Collection<int, CarbonImmutable>  $trendDates
     * @return list<int>
     */
    private function dailyTrend(array $dailyTotals, Collection $trendDates): array
    {
        return array_values($trendDates
            ->map(fn (CarbonImmutable $date): int => $dailyTotals[$date->toDateString()] ?? 0)
            ->values()
            ->all());
    }

    /**
     * @param  Collection<int|string, mixed>  $metrics
     * @return array<string, int>
     */
    private function keyedIntegerMetrics(Collection $metrics): array
    {
        $integerMetrics = [];

        foreach ($metrics as $key => $value) {
            if (! is_string($key)) {
                continue;
            }

            $integerMetrics[$key] = $this->integerValue($value);
        }

        return $integerMetrics;
    }

    private function percentage(int $portion, int $total): float
    {
        if ($total === 0) {
            return 0.0;
        }

        return round(($portion / $total) * 100, 1);
    }

    private function integerValue(mixed $value): int
    {
        return is_numeric($value) ? (int) $value : 0;
    }

    private function hasSignal(mixed $value): bool
    {
        return is_string($value) && trim($value) !== '';
    }

    private function metricKey(mixed $value, string $fallback): string
    {
        if (! is_string($value)) {
            return $fallback;
        }

        $trimmed = trim($value);

        return $trimmed !== '' ? Str::lower($trimmed) : $fallback;
    }

    /**
     * @param  array<string, int>  $metrics
     */
    private function incrementMetric(array &$metrics, string $key): void
    {
        $metrics[$key] = ($metrics[$key] ?? 0) + 1;
    }

    /**
     * @param  array<string, int>  $metrics
     * @return array{key: string, count: int}
     */
    private function topMetric(array $metrics, string $fallback): array
    {
        if ($metrics === []) {
            return ['key' => $fallback, 'count' => 0];
        }

        arsort($metrics);

        /** @var string|null $topKey */
        $topKey = array_key_first($metrics);

        if ($topKey === null) {
            return ['key' => $fallback, 'count' => 0];
        }

        return ['key' => $topKey, 'count' => $metrics[$topKey]];
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    private function leadQualitySegment(array $meta): string
    {
        $timeline = $this->metricKey($meta['implementation_timeline'] ?? $meta['timeline'] ?? null, 'unknown');
        $companySize = $this->metricKey($meta['company_size_band'] ?? null, 'unknown');

        $urgentTimeline = in_array($timeline, ['now', 'this_quarter'], true);
        $largerTeam = in_array($companySize, ['11_50', '51_200', '200_plus'], true);

        if ($urgentTimeline && $largerTeam) {
            return 'high';
        }

        if ($urgentTimeline || $largerTeam || $timeline === 'this_year') {
            return 'warm';
        }

        return 'nurture';
    }

    private function friendlyLabel(string $key): string
    {
        return match ($key) {
            '', 'none' => 'None',
            'landing' => 'Homepage',
            'zoho_one' => 'Zoho One',
            'zoho_marketing_plus' => 'Zoho Marketing Plus',
            'zoho_workplace' => 'Zoho Workplace',
            'direct / unknown' => 'Direct / Unknown',
            'unlabelled campaign' => 'Unlabelled Campaign',
            default => Str::of($key)
                ->replace('_', ' ')
                ->replace('-', ' ')
                ->headline()
                ->toString(),
        };
    }

    private function pageSortOrder(string $pageKey): int
    {
        return match ($pageKey) {
            'landing' => 0,
            'zoho_one' => 1,
            'zoho_marketing_plus' => 2,
            'zoho_workplace' => 3,
            default => 10,
        };
    }
}
