<?php

declare(strict_types=1);

namespace App\Integrations\Zoho;

use App\Models\Submission;

/**
 * Maps a local Submission to the Zoho CRM v2 Lead API payload.
 *
 * PII comes from the encrypted pii_json column; UTM attribution and
 * other context come from meta_json. Only non-PII meta fields are
 * forwarded (utm_*, source_page_key).
 */
class LeadPayloadMapper
{
    /**
     * @return array{data: list<array<string, mixed>>}
     */
    public function map(Submission $submission): array
    {
        /** @var array{name?: mixed, first_name?: mixed, last_name?: mixed, email?: mixed, phone?: mixed, company?: mixed} $pii */
        $pii = $submission->pii_json;

        /** @var array{utm_source?: mixed, utm_medium?: mixed, utm_campaign?: mixed, source_page_key?: mixed} $meta */
        $meta = $submission->meta_json;

        [$firstName, $lastName] = $this->splitName($pii);

        /** @var array<string, mixed> $lead */
        $lead = array_filter([
            'First_Name' => $firstName,
            'Last_Name' => $lastName ?: 'Unknown',
            'Email' => isset($pii['email']) && is_string($pii['email']) ? $pii['email'] : null,
            'Phone' => isset($pii['phone']) && is_string($pii['phone']) ? $pii['phone'] : null,
            'Company' => isset($pii['company']) && is_string($pii['company']) ? $pii['company'] : null,
            'Lead_Source' => $this->mapLeadSource($meta),
            'Description' => $this->buildDescription($meta, $submission->product_key),
        ], fn (mixed $v): bool => $v !== null && $v !== '');

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
     * Maps utm_source to a Zoho Lead_Source picklist value.
     *
     * @param  array{utm_source?: mixed}  $meta
     */
    private function mapLeadSource(array $meta): ?string
    {
        if (! isset($meta['utm_source']) || ! is_string($meta['utm_source'])) {
            return null;
        }

        return match (strtolower($meta['utm_source'])) {
            'google', 'google_ads' => 'Google AdWords',
            'facebook', 'fb' => 'Facebook',
            'linkedin' => 'LinkedIn',
            'organic', 'seo' => 'Organic Search',
            'email', 'newsletter' => 'Email',
            default => 'Web Site',
        };
    }

    /**
     * @param  array{utm_campaign?: mixed, source_page_key?: mixed}  $meta
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

        return implode(' | ', $parts);
    }
}
