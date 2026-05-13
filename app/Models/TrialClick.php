<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\TrialClickFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read int $id
 * @property int|null $journey_id
 * @property string|null $session_id
 * @property string $page_key
 * @property string $product_key
 * @property string $source_page_key
 * @property string $target_url
 * @property string $click_fingerprint
 * @property array<string, mixed> $meta_json
 * @property CarbonImmutable $clicked_at
 */
#[Fillable([
    'journey_id', 'session_id', 'page_key', 'product_key', 'source_page_key',
    'target_url', 'click_fingerprint', 'meta_json', 'clicked_at',
])]
class TrialClick extends Model
{
    /** @use HasFactory<TrialClickFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'meta_json' => 'array',
            'clicked_at' => 'immutable_datetime',
        ];
    }

    /** @return BelongsTo<Journey, $this> */
    public function journey(): BelongsTo
    {
        return $this->belongsTo(Journey::class);
    }
}
