<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;

/**
 * Key-value store for Zoho OAuth credentials. Tokens are short-lived and
 * must be persisted in the database (not config) so they survive worker restarts
 * and can be rotated without redeploying.
 *
 * @property string $key
 * @property string|null $value
 * @property CarbonImmutable|null $expires_at
 */
class ZohoCredential extends Model
{
    protected $primaryKey = 'key';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['key', 'value', 'expires_at'];

    protected function casts(): array
    {
        return [
            'expires_at' => 'immutable_datetime',
        ];
    }

    public static function get(string $key): ?string
    {
        $record = static::query()->find($key);

        if ($record === null) {
            return null;
        }

        return $record->value;
    }

    public static function set(string $key, string $value, ?CarbonImmutable $expiresAt = null): void
    {
        static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'expires_at' => $expiresAt],
        );
    }

    public static function clear(string $key): void
    {
        static::query()->where('key', $key)->delete();
    }

    /**
     * Returns true if the key does not exist, has no value, or has expired.
     */
    public static function isExpired(string $key): bool
    {
        $record = static::query()->find($key);

        if ($record === null || $record->value === null) {
            return true;
        }

        if ($record->expires_at !== null && $record->expires_at->isPast()) {
            return true;
        }

        return false;
    }
}
