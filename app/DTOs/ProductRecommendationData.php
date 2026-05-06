<?php

declare(strict_types=1);

namespace App\DTOs;

final readonly class ProductRecommendationData
{
    /**
     * @param  list<string>  $reasons
     */
    public function __construct(
        public string $headline,
        public array $reasons,
    ) {}
}
