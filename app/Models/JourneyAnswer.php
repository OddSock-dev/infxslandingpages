<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\JourneyAnswerFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read int $id
 * @property int $journey_id
 * @property string $step_key
 * @property string $field_key
 * @property string $value
 */
#[Fillable(['journey_id', 'step_key', 'field_key', 'value'])]
class JourneyAnswer extends Model
{
    /** @use HasFactory<JourneyAnswerFactory> */
    use HasFactory;

    /** @return BelongsTo<Journey, $this> */
    public function journey(): BelongsTo
    {
        return $this->belongsTo(Journey::class);
    }
}
