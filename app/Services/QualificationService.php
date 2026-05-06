<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\AnswerData;

class QualificationService
{
    private const string DEFAULT_PRODUCT_KEY = 'zoho_one';

    /** @var list<string> */
    private const array PRODUCT_PRIORITY = [
        'zoho_one',
        'zoho_marketing_plus',
        'zoho_workplace',
    ];

    /** @var array<string, array<string, array<string, int>>> */
    private const array ROUTING_MATRIX = [
        'primary_goal' => [
            'unify_operations' => [
                'zoho_one' => 5,
            ],
            'measure_marketing' => [
                'zoho_marketing_plus' => 5,
                'zoho_one' => 1,
            ],
            'modernise_collaboration' => [
                'zoho_workplace' => 5,
                'zoho_one' => 1,
            ],
        ],
        'biggest_gap' => [
            'manual_handoffs' => [
                'zoho_one' => 4,
            ],
            'campaign_visibility' => [
                'zoho_marketing_plus' => 4,
            ],
            'email_docs_chat' => [
                'zoho_workplace' => 4,
            ],
        ],
        'team_shape' => [
            'multi_department_growth' => [
                'zoho_one' => 4,
            ],
            'marketing_led' => [
                'zoho_marketing_plus' => 4,
            ],
            'distributed_team' => [
                'zoho_workplace' => 4,
            ],
        ],
        'timeline' => [
            'now' => [
                'zoho_one' => 1,
                'zoho_marketing_plus' => 1,
                'zoho_workplace' => 1,
            ],
            'this_quarter' => [
                'zoho_one' => 1,
                'zoho_marketing_plus' => 1,
                'zoho_workplace' => 1,
            ],
            'this_year' => [
                'zoho_one' => 1,
                'zoho_marketing_plus' => 1,
                'zoho_workplace' => 1,
            ],
        ],
    ];

    /**
     * Determine the product to route a visitor to based on their qualification answers.
     *
     * @param  list<AnswerData>  $answers
     */
    public function route(array $answers): string
    {
        $scores = [
            'zoho_one' => 0,
            'zoho_marketing_plus' => 0,
            'zoho_workplace' => 0,
        ];

        foreach ($answers as $answer) {
            $candidateWeights = self::ROUTING_MATRIX[$answer->fieldKey][strtolower(trim($answer->value))] ?? null;

            if ($candidateWeights === null) {
                continue;
            }

            foreach ($candidateWeights as $productKey => $weight) {
                if (array_key_exists($productKey, $scores)) {
                    $scores[$productKey] += $weight;
                }
            }
        }

        $bestProduct = self::DEFAULT_PRODUCT_KEY;
        $bestScore = $scores[$bestProduct];

        foreach (self::PRODUCT_PRIORITY as $productKey) {
            if ($scores[$productKey] > $bestScore) {
                $bestProduct = $productKey;
                $bestScore = $scores[$productKey];
            }
        }

        return $bestProduct;
    }
}
