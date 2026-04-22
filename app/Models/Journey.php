<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\JourneyStatus;
use Carbon\CarbonImmutable;
use Database\Factories\JourneyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

/**
 * @property-read int $id
 * @property string $journey_token
 * @property string $source_page_key
 * @property string|null $assigned_product_key
 * @property JourneyStatus $status
 * @property string|null $utm_source
 * @property string|null $utm_medium
 * @property string|null $utm_campaign
 * @property string|null $utm_term
 * @property string|null $utm_content
 * @property string|null $referrer
 * @property string|null $ip_hash
 * @property string|null $user_agent
 * @property CarbonImmutable|null $consent_at
 * @property CarbonImmutable $expires_at
 */
#[Fillable([
    'journey_token', 'source_page_key', 'assigned_product_key', 'status',
    'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content',
    'referrer', 'ip_hash', 'user_agent', 'consent_at', 'expires_at',
])]
class Journey extends Model
{
    /** @use HasFactory<JourneyFactory> */
    use HasFactory;

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Journey $journey): void {
            $journey->journey_token ??= (string) Str::ulid();
            $journey->expires_at ??= now()->addHours(24);
        });
    }

    protected function casts(): array
    {
        return [
            'status' => JourneyStatus::class,
            'consent_at' => 'immutable_datetime',
            'expires_at' => 'immutable_datetime',
        ];
    }

    /** @param  Builder<self>  $query */
    public function scopeNotExpired(Builder $query): void
    {
        $query->where('expires_at', '>', now());
    }

    /** @return HasMany<JourneyAnswer, $this> */
    public function answers(): HasMany
    {
        return $this->hasMany(JourneyAnswer::class);
    }

    /** @return HasOne<Submission, $this> */
    public function submission(): HasOne
    {
        return $this->hasOne(Submission::class);
    }
}
