<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Filament\Pages\ZohoConnection;
use App\Models\ZohoCredential;
use App\Services\ZohoConnectionSettings;
use Carbon\CarbonImmutable;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ZohoOAuthController extends Controller
{
    private const int STATE_TTL_MINUTES = 10;

    public function authorize(Request $request, ZohoConnectionSettings $settings): RedirectResponse
    {
        $userId = $request->user()?->getAuthIdentifier();

        if (! is_scalar($userId)) {
            abort(403);
        }

        if (! $settings->isConfigured()) {
            return redirect(ZohoConnection::getUrl())
                ->with('zoho_connection_message', 'Save the Zoho client credentials first.')
                ->with('zoho_connection_status', 'danger');
        }

        $state = Str::random(40);

        Cache::put(
            $this->stateCacheKey($state),
            (string) $userId,
            now()->addMinutes(self::STATE_TTL_MINUTES),
        );

        return redirect()->away($settings->authorizationUrl($state));
    }

    public function callback(Request $request, ZohoConnectionSettings $settings): RedirectResponse
    {
        $state = $request->string('state')->toString();
        $code = $request->string('code')->toString();
        $currentUserId = $request->user()?->getAuthIdentifier();

        if (! is_scalar($currentUserId)) {
            abort(403);
        }

        if ($state === '' || $code === '') {
            return redirect(ZohoConnection::getUrl())
                ->with('zoho_connection_message', 'Zoho did not return a valid authorisation code.')
                ->with('zoho_connection_status', 'danger');
        }

        $cachedUserId = Cache::pull($this->stateCacheKey($state));
        $currentUserId = (string) $currentUserId;

        if (! is_string($cachedUserId) || $cachedUserId === '' || $cachedUserId !== $currentUserId) {
            return redirect(ZohoConnection::getUrl())
                ->with('zoho_connection_message', 'The Zoho authorisation state was invalid or expired.')
                ->with('zoho_connection_status', 'danger');
        }

        $config = $settings->connectionConfig();

        try {
            $response = $settings->baseHttpRequest()
                ->asForm()
                ->post($config['access_token_url'], [
                    'grant_type' => 'authorization_code',
                    'code' => $code,
                    'client_id' => $config['client_id'],
                    'client_secret' => $config['client_secret'],
                    'redirect_uri' => $settings->redirectUrl(),
                ])
                ->throw();
        } catch (RequestException) {
            return redirect(ZohoConnection::getUrl())
                ->with('zoho_connection_message', 'Zoho authorisation failed during the token exchange.')
                ->with('zoho_connection_status', 'danger');
        }

        /** @var array<string, mixed> $body */
        $body = $response->json();
        $accessToken = is_string($body['access_token'] ?? null) ? $body['access_token'] : null;
        $refreshToken = is_string($body['refresh_token'] ?? null) ? $body['refresh_token'] : null;
        $expiresIn = is_int($body['expires_in'] ?? null) ? $body['expires_in'] : 3600;
        $apiDomain = is_string($body['api_domain'] ?? null) ? $body['api_domain'] : null;

        if ($accessToken === null || $refreshToken === null) {
            return redirect(ZohoConnection::getUrl())
                ->with('zoho_connection_message', 'Zoho did not return both an access token and refresh token.')
                ->with('zoho_connection_status', 'danger');
        }

        ZohoCredential::set(
            ZohoCredential::ACCESS_TOKEN,
            $accessToken,
            CarbonImmutable::now()->addSeconds($expiresIn - 60),
        );
        ZohoCredential::set(ZohoCredential::REFRESH_TOKEN, $refreshToken);
        ZohoCredential::set(ZohoCredential::CONNECTED_AT, now()->toIso8601String());

        if ($apiDomain !== null && $apiDomain !== '') {
            ZohoCredential::set(ZohoCredential::API_DOMAIN, $apiDomain);
        }

        return redirect(ZohoConnection::getUrl())
            ->with('zoho_connection_message', 'Zoho is now connected and ready for CRM sync.')
            ->with('zoho_connection_status', 'success');
    }

    private function stateCacheKey(string $state): string
    {
        return "zoho-oauth-state:{$state}";
    }
}
