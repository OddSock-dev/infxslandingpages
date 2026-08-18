<?php

declare(strict_types=1);

namespace App\Integrations\Zoho;

use App\Models\Submission;
use App\Services\ZohoConnectionSettings;
use Illuminate\Support\Str;

class LeadPayloadMapper
{
    public function __construct(
        private readonly ZohoConnectionSettings $settings,
    ) {}

    /**
     * @return array{data: list<array<string, mixed>>}
     */
    public function map(Submission $submission): array
    {
        /** @var array{name?: mixed, first_name?: mixed, last_name?: mixed, email?: mixed, phone?: mixed, company?: mixed} $pii */
        $pii = $submission->pii_json;

        /** @var array<string, mixed> $meta */
        $meta = $submission->meta_json;

        $defaults = $this->settings->leadDefaults();

        [$firstName, $lastName] = $this->splitName($pii);

        /** @var array<string, mixed> $lead */
        $lead = array_filter([
            'First_Name' => $firstName,
            'Last_Name' => $lastName ?: 'Unknown',
            'Email' => isset($pii['email']) && is_string($pii['email']) ? $pii['email'] : null,
            'Phone' => isset($pii['phone']) && is_string($pii['phone']) ? $pii['phone'] : null,
            'Company' => isset($pii['company']) && is_string($pii['company']) ? $pii['company'] : null,
            'Lead_Source' => $this->resolveLeadSource($defaults),
            'Description' => $this->buildDescription($meta, $submission->product_key),
        ], fn (mixed $v): bool => $v !== null && $v !== '');

        $tags = $this->buildTags($defaults);
        if ($tags !== []) {
            $lead['Tag'] = $tags;
        }

        $ownerId = $defaults['lead_owner_id'];
        if ($ownerId !== '') {
            $lead['Owner'] = ['id' => $ownerId];
        }

        $brand = $defaults['lead_brand'];
        if ($brand !== '') {
            $lead['Brand'] = $brand;
        }

        $franchise = $defaults['lead_franchise'];
        if ($franchise !== '') {
            $lead['Franchise'] = $franchise;
        }

        return ['data' => [$lead]];
    }

    /**
     * @param  array{name?: mixed, first_name?: mixed, last_name?: mixed}  $pii
     * @return array{0: string, 1: string}
     */
    private function splitName(array $pii): array
    {
        if (isset($pii['first_name']) && is_string($pii['first_name'])) {
            $first = $pii['first_name'];
            $last = isset($pii['last_name']) && is_string($pii['last_name']) ? $pii['last_name'] : '';

            return [$first, $last];
        }

        if (isset($pii['name']) && is_string($pii['name'])) {
            $parts = explode(' ', trim($pii['name']), 2);

            return [$parts[0], $parts[1] ?? ''];
        }

        return ['', ''];
    }

    /**
     * @param  array{lead_source: string}  $defaults
     */
    private function resolveLeadSource(array $defaults): string
    {
        $source = $defaults['lead_source'];

        return $source !== '' ? $source : 'INFX Zoho Magnet';
    }

    /**
     * @param  array{lead_tags: string}  $defaults
     * @return list<array{name: string}>
     */
    private function buildTags(array $defaults): array
    {
        $raw = $defaults['lead_tags'];
        if (trim($raw) === '') {
            return [];
        }

        $tags = [];
        foreach (explode(',', $raw) as $tag) {
            $tag = trim($tag);
            if ($tag !== '') {
                $tags[] = ['name' => $tag];
            }
        }

        return $tags;
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    private function buildDescription(array $meta, string $productKey): string
    {
        $parts = ["Product interest: {$productKey}"];

        if (isset($meta['utm_campaign']) && is_string($meta['utm_campaign'])) {
            $parts[] = "Campaign: {$meta['utm_campaign']}";
        }

        if (isset($meta['source_page_key']) && is_string($meta['source_page_key'])) {
            $parts[] = "Source page: {$meta['source_page_key']}";
        }

        if (isset($meta['utm_source']) && is_string($meta['utm_source'])) {
            $parts[] = "UTM source: {$meta['utm_source']}";
        }

        $contextLabels = [
            'primary_goal' => 'Primary goal',
            'biggest_gap' => 'Biggest gap',
            'team_shape' => 'Team shape',
            'timeline' => 'Homepage timeline',
            'company_size_band' => 'Company size band',
            'implementation_timeline' => 'Implementation timeline',
            'current_environment' => 'Current environment',
            'priority_outcome' => 'Priority outcome',
        ];

        foreach ($contextLabels as $key => $label) {
            $value = $meta[$key] ?? null;

            if (! is_string($value) || trim($value) === '') {
                continue;
            }

            $parts[] = "{$label}: ".Str::of($value)->replace('_', ' ')->headline()->toString();
        }

        return implode(' | ', $parts);
    }
}
