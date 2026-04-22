<?php

declare(strict_types=1);

namespace App\DTOs;

final readonly class AnswerData
{
    public function __construct(
        public string $stepKey,
        public string $fieldKey,
        public string $value,
    ) {}

    /**
     * @param  array{step_key: string, field_key: string, value: string}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            stepKey: $data['step_key'],
            fieldKey: $data['field_key'],
            value: $data['value'],
        );
    }
}
