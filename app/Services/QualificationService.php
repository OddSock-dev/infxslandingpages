<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\AnswerData;

class QualificationService
{
    private const string DEFAULT_PRODUCT_KEY = 'zoho_one';

    /** @var array<string, string> Maps answer values to product_keys */
    private const array PRODUCT_ROUTING_RULES = [
        'zoho_one' => 'zoho_one',
        'zoho_marketing_plus' => 'zoho_marketing_plus',
        'zoho_workplace' => 'zoho_workplace',
    ];

    /**
     * Determine the product to route a visitor to based on their qualification answers.
     *
     * @param  list<AnswerData>  $answers
     */
    public function route(array $answers): string
    {
        foreach ($answers as $answer) {
            if ($answer->fieldKey === 'product_interest') {
                $candidate = strtolower(trim($answer->value));
                if (array_key_exists($candidate, self::PRODUCT_ROUTING_RULES)) {
                    return $candidate;
                }
            }
        }

        return self::DEFAULT_PRODUCT_KEY;
    }
}
