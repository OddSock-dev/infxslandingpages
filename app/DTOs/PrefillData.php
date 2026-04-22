<?php

declare(strict_types=1);

namespace App\DTOs;

final readonly class PrefillData
{
    /**
     * @param  array<string, string>  $fields  Safe, non-PII fields from journey answers
     */
    public function __construct(
        public ?string $productKey,
        public array $fields,
    ) {}
}
