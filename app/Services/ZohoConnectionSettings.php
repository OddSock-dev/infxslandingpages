<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ZohoCredential;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class ZohoConnectionSettings
{
    /**
     * @return array{
     *     client_id: string,
     *     client_secret: string,
     *     api_domain: string,
     *     accounts_url: string,
     *     authorization_url: string,
     *     access_token_url: string,
     *     scope: string,
     *     auth_query_parameters: string,
     *     ignore_ssl_errors: bool,
     *     token_expired_status_code: int,
     *     allowed_domains: string
     * }
     */
    public function connectionConfig(): array
    {
        $accountsUrl = $this->value(
            ZohoCredential::ACCOUNTS_URL,
            $this->configString('services.zoho.accounts_url', 'https://accounts.zoho.com'),
        );

        return [
            'client_id' => $this->value(ZohoCredential::CLIENT_ID),
            'client_secret' => $this->value(ZohoCredential::CLIENT_SECRET),
            'api_domain' => $this->value(
                ZohoCredential::API_DOMAIN,
                $this->configString('services.zoho.api_domain', 'https://www.zohoapis.com'),
            ),
            'accounts_url' => $accountsUrl,
            'authorization_url' => $this->value(
                ZohoCredential::AUTHORIZATION_URL,
                $accountsUrl !== '' ? "{$accountsUrl}/oauth/v2/auth" : '',
            ),
            'access_token_url' => $this->value(
                ZohoCredential::ACCESS_TOKEN_URL,
                $accountsUrl !== '' ? "{$accountsUrl}/oauth/v2/token" : '',
            ),
            'scope' => $this->value(
                ZohoCredential::SCOPE,
                $this->configString('services.zoho.default_scope'),
            ),
            'auth_query_parameters' => $this->value(
                ZohoCredential::AUTH_QUERY_PARAMETERS,
                'access_type=offline&prompt=consent',
            ),
            'ignore_ssl_errors' => $this->boolValue(ZohoCredential::IGNORE_SSL_ERRORS),
            'token_expired_status_code' => $this->intValue(ZohoCredential::TOKEN_EXPIRED_STATUS_CODE, 401),
            'allowed_domains' => $this->value(ZohoCredential::ALLOWED_DOMAINS),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function formState(): array
    {
        $config = $this->connectionConfig();

        return [
            ...$config,
            'redirect_url' => $this->redirectUrl(),
            'grant_type' => 'Authorization Code',
            'authentication' => 'Header',
        ];
    }

    /**
     * @param  array<string, mixed>  $state
     */
    public function save(array $state): void
    {
        $this->storeString(ZohoCredential::CLIENT_ID, $state['client_id'] ?? null);
        $this->storeString(ZohoCredential::CLIENT_SECRET, $state['client_secret'] ?? null);
        $this->storeString(ZohoCredential::API_DOMAIN, $state['api_domain'] ?? null);
        $this->storeString(ZohoCredential::ACCOUNTS_URL, $state['accounts_url'] ?? null);
        $this->storeString(ZohoCredential::AUTHORIZATION_URL, $state['authorization_url'] ?? null);
        $this->storeString(ZohoCredential::ACCESS_TOKEN_URL, $state['access_token_url'] ?? null);
        $this->storeString(ZohoCredential::SCOPE, $state['scope'] ?? null);
        $this->storeString(ZohoCredential::AUTH_QUERY_PARAMETERS, $state['auth_query_parameters'] ?? null);
        $this->storeString(ZohoCredential::IGNORE_SSL_ERRORS, ! empty($state['ignore_ssl_errors']) ? '1' : '0');
        $tokenExpiredStatusCode = $state['token_expired_status_code'] ?? 401;
        $this->storeString(
            ZohoCredential::TOKEN_EXPIRED_STATUS_CODE,
            is_scalar($tokenExpiredStatusCode) ? (string) $tokenExpiredStatusCode : '401',
        );
        $this->storeString(ZohoCredential::ALLOWED_DOMAINS, $state['allowed_domains'] ?? null);
    }

    public function redirectUrl(): string
    {
        return route('admin.zoho.callback');
    }

    public function isConfigured(): bool
    {
        $config = $this->connectionConfig();

        return $config['client_id'] !== '' && $config['client_secret'] !== '' && $config['authorization_url'] !== '' && $config['access_token_url'] !== '';
    }

    /**
     * @return array{
     *     has_refresh_token: bool,
     *     token_status: string,
     *     token_message: string,
     *     connection_message: string
     * }
     */
    public function summary(): array
    {
        $accessToken = ZohoCredential::query()->find(ZohoCredential::ACCESS_TOKEN);
        $hasRefreshToken = ZohoCredential::get(ZohoCredential::REFRESH_TOKEN) !== null;

        if ($accessToken === null || $accessToken->value === null) {
            $tokenStatus = 'Missing';
            $tokenMessage = 'No access token is stored yet.';
        } elseif ($accessToken->expires_at === null) {
            $tokenStatus = 'Valid';
            $tokenMessage = 'Access token present without an expiry timestamp.';
        } elseif ($accessToken->expires_at->isPast()) {
            $tokenStatus = 'Expired';
            $tokenMessage = 'Reconnect or refresh the Zoho token to resume CRM sync.';
        } elseif ($accessToken->expires_at->diffInMinutes(now()) <= 30) {
            $tokenStatus = 'Expiring Soon';
            $tokenMessage = 'The current access token expires within the next 30 minutes.';
        } else {
            $tokenStatus = 'Valid';
            $tokenMessage = 'The current access token is healthy.';
        }

        return [
            'has_refresh_token' => $hasRefreshToken,
            'token_status' => $tokenStatus,
            'token_message' => $tokenMessage,
            'connection_message' => $hasRefreshToken
                ? 'Zoho refresh token stored. OAuth bootstrap is complete.'
                : 'Save settings and authorise Zoho to bootstrap the refresh token.',
        ];
    }

    public function authorizationUrl(string $state): string
    {
        $config = $this->connectionConfig();
        parse_str($config['auth_query_parameters'], $queryParameters);

        $query = array_merge($queryParameters, [
            'response_type' => 'code',
            'client_id' => $config['client_id'],
            'redirect_uri' => $this->redirectUrl(),
            'scope' => $config['scope'],
            'state' => $state,
        ]);

        return "{$config['authorization_url']}?".http_build_query($query);
    }

    public function baseHttpRequest(): PendingRequest
    {
        $request = Http::connectTimeout(5)
            ->timeout(15)
            ->retry([100, 500, 1000]);

        if ($this->connectionConfig()['ignore_ssl_errors']) {
            $request = $request->withoutVerifying();
        }

        return $request;
    }

    private function value(string $key, string $default = ''): string
    {
        return ZohoCredential::get($key) ?? $default;
    }

    private function boolValue(string $key): bool
    {
        return $this->value($key) === '1';
    }

    private function intValue(string $key, int $default): int
    {
        $value = $this->value($key);

        return is_numeric($value) ? (int) $value : $default;
    }

    private function storeString(string $key, mixed $value): void
    {
        if (! is_string($value)) {
            ZohoCredential::clear($key);

            return;
        }

        $trimmed = trim($value);

        if ($trimmed === '') {
            ZohoCredential::clear($key);

            return;
        }

        ZohoCredential::set($key, $trimmed);
    }

    private function configString(string $key, string $default = ''): string
    {
        $value = config($key);

        return is_string($value) ? $value : $default;
    }
}
