<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
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
#[Fillable(['key', 'value', 'expires_at'])]
class ZohoCredential extends Model
{
    public const string ACCESS_TOKEN = 'access_token';

    public const string REFRESH_TOKEN = 'refresh_token';

    public const string CLIENT_ID = 'client_id';

    public const string CLIENT_SECRET = 'client_secret';

    public const string API_DOMAIN = 'api_domain';

    public const string ACCOUNTS_URL = 'accounts_url';

    public const string AUTHORIZATION_URL = 'authorization_url';

    public const string ACCESS_TOKEN_URL = 'access_token_url';

    public const string SCOPE = 'scope';

    public const string AUTH_QUERY_PARAMETERS = 'auth_query_parameters';

    public const string IGNORE_SSL_ERRORS = 'ignore_ssl_errors';

    public const string TOKEN_EXPIRED_STATUS_CODE = 'token_expired_status_code';

    public const string ALLOWED_DOMAINS = 'allowed_domains';

    public const string CONNECTED_AT = 'connected_at';

    protected $primaryKey = 'key';

    protected $keyType = 'string';

    public $incrementing = false;

    protected function casts(): array
    {
        return [
            'value' => 'encrypted',
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
