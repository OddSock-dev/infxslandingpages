<?php

declare(strict_types=1);

namespace App\Integrations\Zoho;

use App\Models\Journey;
use App\Models\Submission;
use InvalidArgumentException;

/**
 * Maps a local Submission to the Zoho CRM v8 Lead API payload.
 *
 * PII comes from the encrypted pii_json column; UTM attribution and
 * other context come from meta_json. Only non-PII meta fields are
 * forwarded (utm_*, source_page_key).
 */
class LeadPayloadMapper
{
    private const FIXED_FIELDS = [
        'Owner' => [
            'name' => 'Alex Ndhlovu',
            'id' => '2960730000120595001',
            'email' => 'salcorporate@infinitybrands.co.za',
        ],
        'Lead_Stage' => '1. New (Important)',
        'Lead_Status' => 'New',
        'Interest' => 'INFX : ZOHO (Implementation)',
        'Brand' => 'INFX: Zoho',
        'Lead_Source' => 'INFX Zoho Magnet',
        'Franchise' => 'INFX Solutions',
        'Product' => 'Corporate (COR)',
        'Sales_Department' => 'SAL COR: Product Sales',
    ];

    /** @var list<array{name: string}> */
    private const TAGS = [
        ['name' => 'INFXS'],
        ['name' => 'INFX'],
    ];

    /** @var list<array{field_key: string, question: string}> */
    private const HOMEPAGE_QUESTION_ORDER = [
        ['field_key' => 'primary_goal', 'question' => 'What do you need to improve?'],
        ['field_key' => 'biggest_gap', 'question' => 'What is slowing you down?'],
        ['field_key' => 'team_shape', 'question' => 'What does your team look like?'],
        ['field_key' => 'timeline', 'question' => 'When do you want this live?'],
    ];

    /** @var array<string, array<string, string>> */
    private const HOMEPAGE_ANSWER_LABELS = [
        'primary_goal' => [
            'unify_operations' => 'Run the business from one connected set of tools',
            'measure_marketing' => 'See what your marketing is really delivering',
            'modernise_collaboration' => 'Make email, documents, and teamwork feel simpler',
        ],
        'biggest_gap' => [
            'manual_handoffs' => 'Too much manual work between teams',
            'campaign_visibility' => 'Marketing is active, but results are hard to see',
            'email_docs_chat' => 'Email, documents, and chat are all over the place',
        ],
        'team_shape' => [
            'multi_department_growth' => 'A growing team across several departments',
            'marketing_led' => 'A marketing team that needs clearer results',
            'distributed_team' => 'A hybrid or remote team that needs smoother collaboration',
        ],
        'timeline' => [
            'now' => 'Immediately',
            'this_quarter' => 'This quarter',
            'this_year' => 'This year',
        ],
    ];

    /** @var list<array{field_key: string, question: string}> */
    private const PRODUCT_QUESTION_ORDER = [
        ['field_key' => 'company_size_band', 'question' => 'How many people will use this?'],
        ['field_key' => 'implementation_timeline', 'question' => 'When would you like to get started?'],
        ['field_key' => 'current_environment', 'question' => 'What does the current setup look like?'],
        ['field_key' => 'priority_outcome', 'question' => 'What is the main result you want from this product?'],
    ];

    /** @var array<string, array<string, string>> */
    private const PRODUCT_SHARED_ANSWER_LABELS = [
        'company_size_band' => [
            '1_10' => '1-10 people',
            '11_50' => '11-50 people',
            '51_200' => '51-200 people',
            '200_plus' => '200+ people',
        ],
        'implementation_timeline' => [
            'now' => 'Immediately',
            'this_quarter' => 'This quarter',
            'this_year' => 'This year',
            'exploring' => 'Still exploring',
        ],
    ];

    /** @var array<string, array<string, array<string, string>>> */
    private const PRODUCT_PAGE_ANSWER_LABELS = [
        'default' => [
            'current_environment' => [
                'multiple_line_of_business_tools' => 'Too many separate business tools are in use',
                'partially_on_zoho' => 'Some teams use Zoho, but most of the business does not',
                'manual_ops_and_reporting' => 'Too much work and reporting is still manual',
            ],
            'priority_outcome' => [
                'shared_operating_system' => 'One connected setup across the business',
                'executive_visibility' => 'A clearer view across teams, delivery, and finance',
                'automation_at_scale' => 'Less manual work and easier growth',
            ],
        ],
        'zoho_marketing_plus' => [
            'current_environment' => [
                'spreadsheet_campaigns' => 'Campaigns still live in spreadsheets and separate tools',
                'crm_plus_tools' => 'You have a CRM, but marketing still feels disconnected',
                'early_stage_marketing' => 'You are building a more organised marketing team',
            ],
            'priority_outcome' => [
                'attribution_clarity' => 'Clearer reporting on what is working',
                'faster_campaign_delivery' => 'Launch campaigns faster with less tool switching',
                'journey_automation' => 'Better customer communication and marketing automation',
            ],
        ],
        'zoho_workplace' => [
            'current_environment' => [
                'google_microsoft_mix' => 'Google Workspace or Microsoft 365 is mixed with extra tools',
                'legacy_mail_and_files' => 'Mail, files, and chat still feel scattered',
                'privacy_first_shift' => 'You want a privacy-first alternative',
            ],
            'priority_outcome' => [
                'cleaner_collaboration' => 'Smoother collaboration across mail, docs, chat, and meetings',
                'migration_with_less_disruption' => 'Move away from the current suite with less disruption',
                'privacy_and_control' => 'Stronger privacy and more control',
            ],
        ],
    ];

    /** @var array<string, string> */
    private const PRODUCT_LABELS = [
        'zoho_one' => 'Zoho One',
        'zoho_marketing_plus' => 'Zoho Marketing Plus',
        'zoho_workplace' => 'Zoho Workplace',
    ];

    /**
     * @return array{data: list<array<string, mixed>>}
     */
    public function map(Submission $submission): array
    {
        return $this->buildPayload($submission, validateEmail: true);
    }

    /**
     * @return array{data: list<array<string, mixed>>}
     */
    public function snapshot(Submission $submission): array
    {
        return $this->buildPayload($submission, validateEmail: false);
    }

    /**
     * @return array{data: list<array<string, mixed>>}
     */
    private function buildPayload(Submission $submission, bool $validateEmail): array
    {
        $submission->loadMissing('journey.answers');

        /** @var array{name?: mixed, first_name?: mixed, last_name?: mixed, email?: mixed, phone?: mixed, company?: mixed} $pii */
        $pii = $submission->pii_json;

        /** @var array<string, mixed> $meta */
        $meta = $submission->meta_json;

        [$firstName, $lastName] = $this->splitName($pii);
        $email = $validateEmail
            ? $this->validatedEmail($pii['email'] ?? null)
            : $this->stringOrNull($pii['email'] ?? null);
        $phone = $this->stringOrNull($pii['phone'] ?? null);

        /** @var array<string, mixed> $lead */
        $lead = array_filter(array_merge(self::FIXED_FIELDS, [
            'First_Name' => $firstName,
            'Last_Name' => $lastName ?: 'Unknown',
            'Email' => $email,
            'Mobile' => $phone,
            'Phone' => $phone,
            'Company' => $this->stringOrNull($pii['company'] ?? null),
            'Tag' => self::TAGS,
            'Description' => $this->buildDescription($submission, $pii, $meta, $email),
        ]), fn (mixed $value): bool => $value !== null && $value !== '');

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
     * @param  array{name?: mixed, first_name?: mixed, last_name?: mixed, email?: mixed, phone?: mixed, company?: mixed}  $pii
     * @param  array<string, mixed>  $meta
     */
    private function buildDescription(Submission $submission, array $pii, array $meta, ?string $email): string
    {
        $contactSummary = $this->buildContactSummary($pii, $email);
        $sections = [];
        $productLabel = $this->productLabel($submission->product_key);

        if ($productLabel !== null) {
            $sections[] = "Product: {$productLabel}";
        }

        $homepageSection = $this->buildHomepageSection($submission->journey, $contactSummary);

        if ($homepageSection !== null) {
            $sections[] = $homepageSection;
        }

        $productSection = $this->buildProductSection($meta, $submission->product_key, $contactSummary, $homepageSection !== null);

        if ($productSection !== null) {
            $sections[] = $productSection;
        }

        if ($sections === [] && $contactSummary !== '') {
            $sections[] = "Contact details\n{$contactSummary}";
        }

        return implode("\n\n", $sections);
    }

    /**
     * @return array<string, string>
     */
    private function journeyAnswers(?Journey $journey): array
    {
        if (! $journey instanceof Journey) {
            return [];
        }

        $answers = [];

        foreach ($journey->answers as $answer) {
            if (! array_key_exists($answer->field_key, self::HOMEPAGE_ANSWER_LABELS) || array_key_exists($answer->field_key, $answers)) {
                continue;
            }

            $answers[$answer->field_key] = $answer->value;
        }

        return $answers;
    }

    private function buildHomepageSection(?Journey $journey, string $contactSummary): ?string
    {
        $answers = $this->journeyAnswers($journey);

        if ($answers === []) {
            return null;
        }

        $lines = ['Lead magnet answers'];

        foreach (self::HOMEPAGE_QUESTION_ORDER as $index => $question) {
            $value = $answers[$question['field_key']] ?? null;

            if (! is_string($value) || $value === '') {
                continue;
            }

            $answerLabel = self::HOMEPAGE_ANSWER_LABELS[$question['field_key']][$value] ?? null;

            if ($answerLabel === null) {
                continue;
            }

            $lines[] = sprintf('%d. %s %s', $index + 1, $question['question'], $answerLabel);
        }

        if ($contactSummary !== '') {
            $lines[] = "5. Where should we send it? {$contactSummary}";
        }

        return count($lines) > 1 ? implode("\n", $lines) : null;
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    private function buildProductSection(array $meta, string $productKey, string $contactSummary, bool $homepageSectionExists): ?string
    {
        $lines = ['Product page answers'];

        foreach (self::PRODUCT_QUESTION_ORDER as $index => $question) {
            $value = $meta[$question['field_key']] ?? null;

            if (! is_string($value) || trim($value) === '') {
                continue;
            }

            $lines[] = sprintf(
                '%d. %s %s',
                $index + 1,
                $question['question'],
                $this->productAnswerLabel($productKey, $question['field_key'], $value),
            );
        }

        if (! $homepageSectionExists && $contactSummary !== '') {
            $lines[] = "Contact details: {$contactSummary}";
        }

        return count($lines) > 1 ? implode("\n", $lines) : null;
    }

    private function productLabel(string $productKey): ?string
    {
        return self::PRODUCT_LABELS[$productKey] ?? null;
    }

    private function productAnswerLabel(string $productKey, string $fieldKey, string $value): string
    {
        $pageKey = array_key_exists($productKey, self::PRODUCT_PAGE_ANSWER_LABELS) ? $productKey : 'default';

        return self::PRODUCT_SHARED_ANSWER_LABELS[$fieldKey][$value]
            ?? self::PRODUCT_PAGE_ANSWER_LABELS[$pageKey][$fieldKey][$value]
            ?? $value;
    }

    /**
     * @param  array{name?: mixed, first_name?: mixed, last_name?: mixed, email?: mixed, phone?: mixed, company?: mixed}  $pii
     */
    private function buildContactSummary(array $pii, ?string $email): string
    {
        $parts = array_filter([
            $this->fullName($pii),
            $email,
            $this->stringOrNull($pii['phone'] ?? null),
            $this->stringOrNull($pii['company'] ?? null),
        ], static fn (?string $value): bool => $value !== null && $value !== '');

        return implode(' / ', $parts);
    }

    /**
     * @param  array{name?: mixed, first_name?: mixed, last_name?: mixed}  $pii
     */
    private function fullName(array $pii): ?string
    {
        $name = $this->stringOrNull($pii['name'] ?? null);

        if ($name !== null) {
            return $name;
        }

        $parts = array_filter([
            $this->stringOrNull($pii['first_name'] ?? null),
            $this->stringOrNull($pii['last_name'] ?? null),
        ], static fn (?string $value): bool => $value !== null && $value !== '');

        if ($parts === []) {
            return null;
        }

        return implode(' ', $parts);
    }

    private function stringOrNull(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed !== '' ? $trimmed : null;
    }

    private function validatedEmail(mixed $value): string
    {
        if (! is_string($value)) {
            throw new InvalidArgumentException('Submission email is missing for Zoho CRM.');
        }

        $normalized = preg_replace('/\s+/u', '', trim($value));

        if (! is_string($normalized) || $normalized === '') {
            throw new InvalidArgumentException('Submission email is missing for Zoho CRM.');
        }

        if (filter_var($normalized, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidArgumentException('Submission email is invalid for Zoho CRM.');
        }

        return $normalized;
    }
}
