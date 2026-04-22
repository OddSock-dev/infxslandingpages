<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\SubmissionData;
use App\Enums\CrmStatus;
use App\Enums\JourneyStatus;
use App\Models\Journey;
use App\Models\Submission;
use Illuminate\Support\Facades\DB;

class SubmissionRecorder
{
    /**
     * Persist a lead submission and link it to the associated journey (if any).
     *
     * UTM attribution is merged from the linked journey so the meta_json always
     * contains the original marketing context captured at qualification time.
     *
     * Note: job dispatch (Zoho sync) is wired in Phase 3. This method only
     * persists the record and marks crm_status as pending.
     */
    public function record(SubmissionData $data): Submission
    {
        return DB::transaction(function () use ($data): Submission {
            $journey = null;

            if ($data->journeyToken !== null) {
                /** @var Journey|null $journey */
                $journey = Journey::query()
                    ->where('journey_token', $data->journeyToken)
                    ->notExpired()
                    ->whereNot('status', JourneyStatus::Submitted->value)
                    ->lockForUpdate()
                    ->first();
            }

            $metaJson = $this->buildMetaJson($data->metaJson, $journey);

            $submission = Submission::create([
                'journey_id' => $journey?->id,
                'page_key' => $data->productKey,
                'product_key' => $data->productKey,
                'pii_json' => [
                    'name' => $data->name,
                    'email' => $data->email,
                    'phone' => $data->phone,
                    'company' => $data->company,
                ],
                'meta_json' => $metaJson,
                'crm_status' => CrmStatus::Pending,
                'submitted_at' => now(),
            ]);

            if ($journey !== null) {
                $journey->update(['status' => JourneyStatus::Submitted]);
            }

            return $submission;
        });
    }

    /**
     * Merge server-derived meta with UTM attribution from the linked journey.
     *
     * @param  array<string, mixed>  $serverMeta
     * @return array<string, mixed>
     */
    private function buildMetaJson(array $serverMeta, ?Journey $journey): array
    {
        if ($journey === null) {
            return $serverMeta;
        }

        /** @var array<string, mixed> $journeyMeta */
        $journeyMeta = array_filter([
            'utm_source' => $journey->utm_source,
            'utm_medium' => $journey->utm_medium,
            'utm_campaign' => $journey->utm_campaign,
            'utm_term' => $journey->utm_term,
            'utm_content' => $journey->utm_content,
            'source_page_key' => $journey->source_page_key,
            'journey_referrer' => $journey->referrer,
        ]);

        return array_merge($serverMeta, $journeyMeta);
    }
}
