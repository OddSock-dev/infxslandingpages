<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Journey;
use App\Services\PrefillService;
use Illuminate\Http\JsonResponse;

class JourneyController extends Controller
{
    public function __construct(
        private readonly PrefillService $prefillService,
    ) {}

    public function prefill(string $token): JsonResponse
    {
        /** @var Journey|null $journey */
        $journey = Journey::query()
            ->where('journey_token', $token)
            ->notExpired()
            ->first();

        if ($journey === null) {
            return response()->json(['message' => 'Journey not found or expired.'], 404);
        }

        $prefillData = $this->prefillService->extractSafeFields($journey);

        return response()->json([
            'product_key' => $prefillData->productKey,
            'fields' => $prefillData->fields,
        ]);
    }
}
