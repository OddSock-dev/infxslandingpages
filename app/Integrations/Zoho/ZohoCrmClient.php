<?php

declare(strict_types=1);

namespace App\Integrations\Zoho;

use App\Models\ZohoCredential;
use App\Services\ZohoConnectionSettings;
use Carbon\CarbonImmutable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;

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

    public function __construct(
        private readonly ZohoConnectionSettings $settings,
    ) {}

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
        $config = $this->settings->connectionConfig();
        $lock = Cache::lock(self::TOKEN_REFRESH_LOCK, self::TOKEN_REFRESH_LOCK_SECONDS);

        $lock->block(self::TOKEN_REFRESH_LOCK_SECONDS);

        try {
            // Re-check inside the lock — another worker may have just refreshed.
            if (! ZohoCredential::isExpired('access_token')) {
                return;
            }

            $refreshToken = ZohoCredential::get(ZohoCredential::REFRESH_TOKEN);

            if ($refreshToken === null) {
                throw new \RuntimeException('No Zoho refresh token found. Connect Zoho from the admin panel first.');
            }

            $response = $this->tokenRequest()
                ->post($config['access_token_url'], [
                    'grant_type' => 'refresh_token',
                    'client_id' => $config['client_id'],
                    'client_secret' => $config['client_secret'],
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
                ZohoCredential::ACCESS_TOKEN,
                $accessToken,
                CarbonImmutable::now()->addSeconds($expiresIn - 60),
            );

            // Some Zoho apps rotate the refresh token on each use.
            if (isset($body['refresh_token']) && is_string($body['refresh_token'])) {
                ZohoCredential::set(ZohoCredential::REFRESH_TOKEN, $body['refresh_token']);
            }
        } finally {
            $lock->forceRelease();
        }
    }

    /**
     * Creates a lead in Zoho CRM via the v8 API.
     *
     * @param  array<string, mixed>  $payload  Pre-built Zoho API payload (must contain 'data' key)
     * @return array<string, mixed> Parsed JSON response from Zoho
     *
     * @throws RequestException on HTTP failure
     */
    public function createLead(array $payload): array
    {
        $config = $this->settings->connectionConfig();
        $response = $this->leadRequest($this->getAccessToken())
            ->post("{$config['api_domain']}/crm/v8/Leads", $payload);

        if ($response->status() === $config['token_expired_status_code']) {
            ZohoCredential::clear(ZohoCredential::ACCESS_TOKEN);

            $response = $this->leadRequest($this->getAccessToken())
                ->post("{$config['api_domain']}/crm/v8/Leads", $payload);
        }

        $response->throw();

        /** @var array<string, mixed> $body */
        $body = $response->json() ?? [];

        return $body;
    }

    private function tokenRequest(): PendingRequest
    {
        return $this->baseRequest()->asForm();
    }

    private function leadRequest(string $token): PendingRequest
    {
        return $this->baseRequest(
            nonRetryableStatus: $this->settings->connectionConfig()['token_expired_status_code'],
            throw: false,
        )
            ->withHeader('Authorization', "Zoho-oauthtoken {$token}");
    }

    private function baseRequest(?int $nonRetryableStatus = null, bool $throw = true): PendingRequest
    {
        $config = $this->settings->connectionConfig();
        $request = Http::connectTimeout(5)
            ->timeout(15)
            ->retry(
                [100, 500, 1000],
                0,
                function (Throwable $exception, PendingRequest $request) use ($nonRetryableStatus): bool {
                    if ($exception instanceof ConnectionException) {
                        return true;
                    }

                    if (! $exception instanceof RequestException) {
                        return false;
                    }

                    if ($nonRetryableStatus !== null && $exception->response->status() === $nonRetryableStatus) {
                        return false;
                    }

                    return $exception->response->serverError();
                },
                $throw,
            );

        if ($config['ignore_ssl_errors']) {
            $request = $request->withoutVerifying();
        }

        return $request;
    }
}
