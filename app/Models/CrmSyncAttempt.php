<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SyncAttemptStatus;
use Carbon\CarbonImmutable;
use Database\Factories\CrmSyncAttemptFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read int $id
 * @property int $submission_id
 * @property string $provider
 * @property string $action
 * @property array<string, mixed> $request_payload Encrypted at rest — may contain PII
 * @property array<string, mixed>|null $response_payload Encrypted at rest
 * @property SyncAttemptStatus $status
 * @property string|null $error_code
 * @property string|null $error_message
 * @property CarbonImmutable $attempted_at
 */
#[Fillable([
    'submission_id', 'provider', 'action',
    'request_payload', 'response_payload', 'status',
    'error_code', 'error_message', 'attempted_at',
])]
class CrmSyncAttempt extends Model
{
    /** @use HasFactory<CrmSyncAttemptFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'request_payload' => 'encrypted:array',
            'response_payload' => 'encrypted:array',
            'status' => SyncAttemptStatus::class,
            'attempted_at' => 'immutable_datetime',
        ];
    }

    /** @return BelongsTo<Submission, $this> */
    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }
}
