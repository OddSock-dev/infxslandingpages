<?php

declare(strict_types=1);

namespace App\Actions\Zoho;

use App\Enums\CrmStatus;
use App\Enums\SyncAttemptStatus;
use App\Integrations\Zoho\LeadPayloadMapper;
use App\Integrations\Zoho\ZohoCrmClient;
use App\Models\CrmSyncAttempt;
use App\Models\Submission;
use App\Support\CrmPayloadRedactor;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
                    'error_code' => $this->resolveErrorCode($e),
                    'error_message' => $this->resolveErrorMessage($e),
                    'response_payload' => $this->extractResponsePayload($e),
                ]);
                $submission->update(['crm_status' => CrmStatus::Failed]);
            });

            throw $e;
        }
    }

    private function resolveErrorCode(\Throwable $exception): string
    {
        if ($exception instanceof ConnectionException) {
            return 'CONNECTION_ERROR';
        }

        if (! $exception instanceof RequestException || $exception->response === null) {
            return 'SYNC_ERROR';
        }

        return match ($exception->response->status()) {
            401 => 'HTTP_401_UNAUTHORIZED',
            403 => 'HTTP_403_FORBIDDEN',
            404 => 'HTTP_404_NOT_FOUND',
            409 => 'HTTP_409_CONFLICT',
            422 => 'HTTP_422_VALIDATION',
            429 => 'HTTP_429_RATE_LIMITED',
            500, 502, 503, 504 => 'HTTP_5XX_SERVER_ERROR',
            default => sprintf('HTTP_%d', $exception->response->status()),
        };
    }

    private function resolveErrorMessage(\Throwable $exception): string
    {
        if (! $exception instanceof RequestException || $exception->response === null) {
            return CrmPayloadRedactor::redactMessage($exception->getMessage()) ?? 'CRM sync failed.';
        }

        $summary = [sprintf('HTTP %d', $exception->response->status())];
        $messages = $this->collectResponseMessages($exception);

        if ($messages !== []) {
            $summary[] = implode(' | ', $messages);
        } else {
            $body = Str::of($exception->response->body())->squish()->limit(240)->toString();

            if ($body !== '') {
                $summary[] = $body;
            }
        }

        return CrmPayloadRedactor::redactMessage(implode(': ', $summary)) ?? 'CRM sync failed.';
    }

    /**
     * @return array<int|string, mixed>|null
     */
    private function extractResponsePayload(\Throwable $exception): ?array
    {
        if (! $exception instanceof RequestException || $exception->response === null) {
            return null;
        }

        $json = $exception->response->json();

        if (is_array($json)) {
            return $json;
        }

        $body = trim($exception->response->body());

        if ($body === '') {
            return ['status' => $exception->response->status()];
        }

        return [
            'status' => $exception->response->status(),
            'body' => $body,
        ];
    }

    /**
     * @return list<string>
     */
    private function collectResponseMessages(RequestException $exception): array
    {
        if ($exception->response === null) {
            return [];
        }

        $payload = $exception->response->json();

        if (! is_array($payload)) {
            return [];
        }

        $messages = [];

        foreach ([
            data_get($payload, 'message'),
            data_get($payload, 'error.message'),
            data_get($payload, 'data.0.message'),
            data_get($payload, 'data.0.details.api_name'),
        ] as $candidate) {
            if (! is_string($candidate) || trim($candidate) === '') {
                continue;
            }

            $messages[] = Str::of($candidate)->squish()->limit(120)->toString();
        }

        return array_values(array_unique($messages));
    }
}
