<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\ProductRecommendationData;

final class ProductRecommendationService
{
    /** @var list<string> */
    private const array PRIORITY_FIELDS = [
        'primary_goal',
        'biggest_gap',
        'team_shape',
        'timeline',
    ];

    /** @var array<string, array<string, string>> */
    private const array REASONS = [
        'primary_goal' => [
            'unify_operations' => 'You want your teams working from one connected system instead of juggling disconnected tools.',
            'measure_marketing' => 'You need a clearer view of which marketing activity is working and what it is worth.',
            'modernise_collaboration' => 'You want email, documents, chat, and teamwork to feel simpler day to day.',
        ],
        'biggest_gap' => [
            'manual_handoffs' => 'Too much manual work is slowing the business down, so a more connected platform would save time and reduce duplication.',
            'campaign_visibility' => 'It is hard to see which campaigns are performing, so clearer reporting and easier coordination matter most.',
            'email_docs_chat' => 'Your current email, document, and chat setup feels fragmented, so consolidating those day-to-day tools will create immediate relief.',
        ],
        'team_shape' => [
            'multi_department_growth' => 'Several departments need to work from the same reliable information as the business grows.',
            'marketing_led' => 'A marketing-led team will benefit from one place to plan campaigns, automate customer communication, and review results.',
            'distributed_team' => 'A distributed team needs simpler collaboration patterns and a platform people can adopt consistently across locations.',
        ],
        'timeline' => [
            'now' => 'You want to move quickly, so it helps to see the best option and next step right away.',
            'this_quarter' => 'You want to make progress this quarter, so it helps to compare the right option now and choose your next step with confidence.',
            'this_year' => 'You are planning ahead this year, which gives you space to compare options carefully before making a decision.',
        ],
    ];

    /**
     * @param  array<string, string>  $fields
     */
    public function build(string $productName, array $fields): ?ProductRecommendationData
    {
        $reasons = [];

        foreach (self::PRIORITY_FIELDS as $fieldKey) {
            $value = $fields[$fieldKey] ?? null;

            if (! is_string($value) || $value === '') {
                continue;
            }

            $reason = self::REASONS[$fieldKey][$value] ?? null;

            if ($reason === null) {
                continue;
            }

            $reasons[] = $reason;

            if (count($reasons) === 3) {
                break;
            }
        }

        if ($reasons === []) {
            return null;
        }

        return new ProductRecommendationData(
            headline: "Based on what you shared, {$productName} looks like a strong fit for your business. Here is why:",
            reasons: $reasons,
        );
    }
}
