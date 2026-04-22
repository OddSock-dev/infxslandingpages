<?php

declare(strict_types=1);

namespace App\DTOs;

final readonly class QualificationAnswersData
{
    /**
     * @param  list<AnswerData>  $answers
     */
    public function __construct(
        public string $sourcePageKey,
        public array $answers,
        public bool $consent,
        public ?string $utmSource = null,
        public ?string $utmMedium = null,
        public ?string $utmCampaign = null,
        public ?string $utmTerm = null,
        public ?string $utmContent = null,
        public ?string $referrer = null,
    ) {}

    /**
     * @param  array{
     *     source_page_key: string,
     *     answers: list<array{step_key: string, field_key: string, value: string}>,
     *     consent: bool,
     *     utm_source?: string|null,
     *     utm_medium?: string|null,
     *     utm_campaign?: string|null,
     *     utm_term?: string|null,
     *     utm_content?: string|null,
     *     referrer?: string|null,
     * }  $validated
     */
    public static function fromValidated(array $validated): self
    {
        $answers = array_map(
            static fn (array $answer): AnswerData => AnswerData::fromArray($answer),
            $validated['answers'],
        );

        return new self(
            sourcePageKey: $validated['source_page_key'],
            answers: $answers,
            consent: $validated['consent'],
            utmSource: $validated['utm_source'] ?? null,
            utmMedium: $validated['utm_medium'] ?? null,
            utmCampaign: $validated['utm_campaign'] ?? null,
            utmTerm: $validated['utm_term'] ?? null,
            utmContent: $validated['utm_content'] ?? null,
            referrer: $validated['referrer'] ?? null,
        );
    }
}
