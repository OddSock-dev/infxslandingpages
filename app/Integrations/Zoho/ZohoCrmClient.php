<?php

declare(strict_types=1);

namespace App\Integrations\Zoho;

use App\Models\ZohoCredential;
use Carbon\CarbonImmutable;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Low-level Zoho CRM API client.
 *
 * Tokens are stored in the `zoho_credentials` table. The access token is
 * refreshed automatically when expired; a distributed lock prevents concurrent
 * workers from issuing duplicate refresh calls.
 *
 * Bootstrap: seed the initial refresh_token once with
 *   php artisan zoho:set-token refresh_token <value>
 */
class ZohoCrmClient
{
    private const TOKEN_REFRESH_LOCK = 'zoho_token_refresh';

    private const TOKEN_REFRESH_LOCK_SECONDS = 30;

    /** @param  array{client_id: string, client_secret: string, api_domain: string, accounts_url: string}  $config */
    public function __construct(private readonly array $config) {}

    /**
     * Returns a valid access token, refreshing first if the stored one is expired.
     *
     * @throws \RuntimeException when no refresh token is available
     */
    public function getAccessToken(): string
    {
        if (! ZohoCredential::isExpired('access_token')) {
            $token = ZohoCredential::get('access_token');

            if ($token !== null) {
                return $token;
            }
        }

        $this->refreshToken();

        $token = ZohoCredential::get('access_token');

        if ($token === null) {
            throw new \RuntimeException('Zoho access token unavailable after refresh.');
        }

        return $token;
    }

    /**
     * Exchanges the stored refresh token for a new access token.
     * Uses a distributed lock to prevent concurrent refresh calls.
     *
     * @throws \RuntimeException when no refresh token is in the database
     * @throws RequestException on HTTP failure
     */
    public function refreshToken(): void
    {
        $lock = Cache::lock(self::TOKEN_REFRESH_LOCK, self::TOKEN_REFRESH_LOCK_SECONDS);

        $lock->block(self::TOKEN_REFRESH_LOCK_SECONDS);

        try {
            // Re-check inside the lock — another worker may have just refreshed.
            if (! ZohoCredential::isExpired('access_token')) {
                return;
            }

            $refreshToken = ZohoCredential::get('refresh_token');

            if ($refreshToken === null) {
                throw new \RuntimeException('No Zoho refresh token found. Seed one with: php artisan zoho:set-token refresh_token <value>');
            }

            $response = Http::asForm()
                ->post("{$this->config['accounts_url']}/oauth/v2/token", [
                    'grant_type' => 'refresh_token',
                    'client_id' => $this->config['client_id'],
                    'client_secret' => $this->config['client_secret'],
                    'refresh_token' => $refreshToken,
                ])
                ->throw();

            /** @var array<string, mixed> $body */
            $body = $response->json();

            $accessToken = isset($body['access_token']) && is_string($body['access_token'])
                ? $body['access_token']
                : throw new \RuntimeException('Zoho token response missing access_token.');

            $expiresIn = isset($body['expires_in']) && is_int($body['expires_in'])
                ? $body['expires_in']
                : 3600;

            ZohoCredential::set(
                'access_token',
                $accessToken,
                CarbonImmutable::now()->addSeconds($expiresIn - 60),
            );

            // Some Zoho apps rotate the refresh token on each use.
            if (isset($body['refresh_token']) && is_string($body['refresh_token'])) {
                ZohoCredential::set('refresh_token', $body['refresh_token']);
            }
        } finally {
            $lock->forceRelease();
        }
    }

    /**
     * Creates a lead in Zoho CRM via the v2 API.
     *
     * @param  array<string, mixed>  $payload  Pre-built Zoho API payload (must contain 'data' key)
     * @return array<string, mixed> Parsed JSON response from Zoho
     *
     * @throws RequestException on HTTP failure
     */
    public function createLead(array $payload): array
    {
        $response = Http::withToken($this->getAccessToken())
            ->post("{$this->config['api_domain']}/crm/v2/Leads", $payload)
            ->throw();

        /** @var array<string, mixed> $body */
        $body = $response->json() ?? [];

        return $body;
    }
}
