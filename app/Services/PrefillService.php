<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\PrefillData;
use App\Models\Journey;

class PrefillService
{
    /** Fields that contain PII and must never be exposed via the prefill endpoint. */
    private const array PII_FIELD_KEYS = [
        'email', 'name', 'first_name', 'last_name',
        'phone', 'mobile', 'company', 'organisation', 'organization',
    ];

    /**
     * Extract safe, non-PII fields from a journey's qualification answers.
     */
    public function extractSafeFields(Journey $journey): PrefillData
    {
        /** @var array<string, string> $safeFields */
        $safeFields = [];

        foreach ($journey->answers as $answer) {
            if (! in_array($answer->field_key, self::PII_FIELD_KEYS, strict: true)) {
                $safeFields[$answer->field_key] = $answer->value;
            }
        }

        return new PrefillData(
            productKey: $journey->assigned_product_key,
            fields: $safeFields,
        );
    }
}
