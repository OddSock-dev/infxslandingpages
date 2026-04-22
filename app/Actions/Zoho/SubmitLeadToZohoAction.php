<?php

declare(strict_types=1);

namespace App\Actions\Zoho;

use App\Enums\CrmStatus;
use App\Enums\SyncAttemptStatus;
use App\Integrations\Zoho\LeadPayloadMapper;
use App\Integrations\Zoho\ZohoCrmClient;
use App\Models\CrmSyncAttempt;
use App\Models\Submission;
use Illuminate\Support\Facades\DB;

/**
 * Executes a single Zoho CRM lead-creation attempt for a submission.
 *
 * Flow:
 *  1. Build the Zoho payload via LeadPayloadMapper (fail fast, no attempt row yet)
 *  2. Record a CrmSyncAttempt with status=Pending and the request payload
 *  3. Call Zoho CRM API
 *  4. Wrap local DB updates in a transaction so attempt + submission stay in sync
 *  5. Re-throw any exception so the queued job can retry
 */
class SubmitLeadToZohoAction
{
    public function __construct(
        private readonly ZohoCrmClient $client,
        private readonly LeadPayloadMapper $mapper,
    ) {}

    /**
     * @throws \Throwable re-throws any exception after recording the failure
     */
    public function execute(Submission $submission): void
    {
        $payload = $this->mapper->map($submission);

        $attempt = CrmSyncAttempt::create([
            'submission_id' => $submission->id,
            'provider' => 'zoho_crm',
            'action' => 'create_lead',
            'request_payload' => $payload,
            'status' => SyncAttemptStatus::Pending,
            'attempted_at' => now(),
        ]);

        try {
            $response = $this->client->createLead($payload);

            DB::transaction(function () use ($attempt, $submission, $response): void {
                $attempt->update([
                    'status' => SyncAttemptStatus::Success,
                    'response_payload' => $response,
                ]);
                $submission->update(['crm_status' => CrmStatus::Synced]);
            });
        } catch (\Throwable $e) {
            DB::transaction(function () use ($attempt, $submission, $e): void {
                $attempt->update([
                    'status' => SyncAttemptStatus::Failed,
                    'error_code' => 'API_ERROR',
                    'error_message' => $e->getMessage(),
                ]);
                $submission->update(['crm_status' => CrmStatus::Failed]);
            });

            throw $e;
        }
    }
}
