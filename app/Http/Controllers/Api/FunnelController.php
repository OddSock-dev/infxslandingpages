<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\DTOs\QualificationAnswersData;
use App\DTOs\SubmissionData;
use App\Enums\JourneyStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\QualifyRequest;
use App\Http\Requests\SubmitRequest;
use App\Models\Journey;
use App\Models\JourneyAnswer;
use App\Services\QualificationService;
use App\Services\SubmissionRecorder;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;

class FunnelController extends Controller
{
    public function __construct(
        private readonly QualificationService $qualificationService,
        private readonly SubmissionRecorder $submissionRecorder,
    ) {}

    public function qualify(QualifyRequest $request): JsonResponse
    {
        /** @var array{source_page_key: string, answers: list<array{step_key: string, field_key: string, value: string}>, consent: bool, utm_source?: string|null, utm_medium?: string|null, utm_campaign?: string|null, utm_term?: string|null, utm_content?: string|null, referrer?: string|null} $validated */
        $validated = $request->validated();
        $data = QualificationAnswersData::fromValidated($validated);

        $journey = Journey::create([
            'source_page_key' => $data->sourcePageKey,
            'status' => JourneyStatus::Qualifying,
            'utm_source' => $data->utmSource,
            'utm_medium' => $data->utmMedium,
            'utm_campaign' => $data->utmCampaign,
            'utm_term' => $data->utmTerm,
            'utm_content' => $data->utmContent,
            'referrer' => $data->referrer,
            'ip_hash' => hash('sha256', $request->ip() ?? ''),
            'user_agent' => $request->userAgent(),
            'consent_at' => now(),
        ]);

        $now = now()->toDateTimeString();
        $answerRows = array_map(fn (mixed $answer): array => [
            'journey_id' => $journey->id,
            'step_key' => $answer->stepKey,
            'field_key' => $answer->fieldKey,
            'value' => $answer->value,
            'created_at' => $now,
            'updated_at' => $now,
        ], $data->answers);

        JourneyAnswer::insert($answerRows);

        $productKey = $this->qualificationService->route($data->answers);

        $journey->update([
            'assigned_product_key' => $productKey,
            'status' => JourneyStatus::Routed,
        ]);

        return response()->json([
            'journey_token' => $journey->journey_token,
            'product_key' => $productKey,
        ], 201);
    }

    public function submit(SubmitRequest $request): JsonResponse
    {
        /** @var array{product_key: string, journey_token?: string|null, name: string, email: string, phone?: string|null, company?: string|null} $validated */
        $validated = $request->validated();

        if (isset($validated['journey_token'])) {
            $token = $validated['journey_token'];

            /** @var Journey|null $journey */
            $journey = Journey::query()
                ->where('journey_token', $token)
                ->notExpired()
                ->first();

            if ($journey === null) {
                return response()->json([
                    'message' => 'Journey token is invalid or expired.',
                ], 422);
            }

            if ($journey->status === JourneyStatus::Submitted) {
                return response()->json([
                    'message' => 'This journey has already been submitted.',
                ], 409);
            }

            if ($journey->assigned_product_key !== $validated['product_key']) {
                return response()->json([
                    'message' => 'The product key does not match the journey.',
                ], 422);
            }
        }

        $serverMeta = [
            'ip_hash' => hash('sha256', $request->ip() ?? ''),
            'user_agent' => $request->userAgent(),
            'referrer' => $request->header('Referer'),
        ];

        $data = SubmissionData::fromValidated($validated, $serverMeta);

        try {
            $this->submissionRecorder->record($data);
        } catch (UniqueConstraintViolationException) {
            return response()->json([
                'message' => 'A submission for this journey already exists.',
            ], 409);
        }

        return response()->json(['message' => 'Submission received.'], 201);
    }
}
