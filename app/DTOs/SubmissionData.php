<?php

declare(strict_types=1);

namespace App\DTOs;

final readonly class SubmissionData
{
    /**
     * @param  array<string, mixed>  $metaJson  Server-derived metadata (IP hash, UA, referrer)
     */
    public function __construct(
        public string $productKey,
        public string $name,
        public string $email,
        public ?string $phone,
        public ?string $company,
        public array $metaJson,
        public ?string $journeyToken = null,
    ) {}

    /**
     * @param  array{
     *     product_key: string,
     *     name: string,
     *     email: string,
     *     phone?: string|null,
     *     company?: string|null,
     *     journey_token?: string|null,
     * }  $validated
     * @param  array<string, mixed>  $serverMeta
     */
    public static function fromValidated(array $validated, array $serverMeta): self
    {
        return new self(
            productKey: $validated['product_key'],
            name: $validated['name'],
            email: $validated['email'],
            phone: $validated['phone'] ?? null,
            company: $validated['company'] ?? null,
            metaJson: $serverMeta,
            journeyToken: $validated['journey_token'] ?? null,
        );
    }
}
