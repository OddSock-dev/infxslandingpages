<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CrmStatus;
use Carbon\CarbonImmutable;
use Database\Factories\SubmissionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read int $id
 * @property int|null $journey_id
 * @property string $page_key
 * @property string $product_key
 * @property array<string, mixed> $pii_json Encrypted at rest — name, email, phone, company
 * @property array<string, mixed> $meta_json
 * @property CrmStatus $crm_status
 * @property CarbonImmutable $submitted_at
 */
#[Fillable([
    'journey_id', 'page_key', 'product_key',
    'pii_json', 'meta_json', 'crm_status', 'submitted_at',
])]
class Submission extends Model
{
    /** @use HasFactory<SubmissionFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'pii_json' => 'encrypted:array',
            'meta_json' => 'array',
            'crm_status' => CrmStatus::class,
            'submitted_at' => 'immutable_datetime',
        ];
    }

    /** @param  Builder<self>  $query */
    public function scopePendingSync(Builder $query): void
    {
        $query->where('crm_status', CrmStatus::Pending);
    }

    /** @return BelongsTo<Journey, $this> */
    public function journey(): BelongsTo
    {
        return $this->belongsTo(Journey::class);
    }

    /** @return HasMany<CrmSyncAttempt, $this> */
    public function syncAttempts(): HasMany
    {
        return $this->hasMany(CrmSyncAttempt::class);
    }
}
