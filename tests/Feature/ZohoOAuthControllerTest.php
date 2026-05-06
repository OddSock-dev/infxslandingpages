<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Filament\Pages\ZohoConnection;
use App\Models\User;
use App\Models\ZohoCredential;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ZohoOAuthControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.zoho.api_domain', 'https://www.zohoapis.test');
        config()->set('services.zoho.accounts_url', 'https://accounts.zoho.test');
        config()->set('services.zoho.default_scope', 'ZohoCRM.modules.ALL');

        $this->storeConnectionSettings();

        $this->admin = User::factory()->create();
        $this->actingAs($this->admin);

        Http::preventStrayRequests();
    }

    public function test_authorize_requires_saved_connection_settings(): void
    {
        ZohoCredential::clear(ZohoCredential::CLIENT_ID);
        ZohoCredential::clear(ZohoCredential::CLIENT_SECRET);

        Http::fake();

        $response = $this->get(route('admin.zoho.authorize'));

        $response->assertRedirect(ZohoConnection::getUrl());
        $response->assertSessionHas('zoho_connection_message', 'Save the Zoho client credentials first.');
        $response->assertSessionHas('zoho_connection_status', 'danger');

        Http::assertNothingSent();
    }

    public function test_callback_exchanges_code_and_stores_encrypted_tokens(): void
    {
        Http::fake([
            'https://accounts.zoho.test/oauth/v2/token' => Http::response([
                'access_token' => 'oauth-access-token',
                'refresh_token' => 'oauth-refresh-token',
                'expires_in' => 3600,
                'api_domain' => 'https://www.zohoapis.eu',
            ], 200),
        ]);

        $state = $this->authorizeAndExtractState();

        $response = $this->get(route('admin.zoho.callback', [
            'state' => $state,
            'code' => 'oauth-authorisation-code',
        ]));

        $response->assertRedirect(ZohoConnection::getUrl());
        $response->assertSessionHas('zoho_connection_message', 'Zoho is now connected and ready for CRM sync.');
        $response->assertSessionHas('zoho_connection_status', 'success');

        $this->assertSame('oauth-access-token', ZohoCredential::get(ZohoCredential::ACCESS_TOKEN));
        $this->assertSame('oauth-refresh-token', ZohoCredential::get(ZohoCredential::REFRESH_TOKEN));
        $this->assertSame('https://www.zohoapis.eu', ZohoCredential::get(ZohoCredential::API_DOMAIN));
        $this->assertNotNull(ZohoCredential::get(ZohoCredential::CONNECTED_AT));

        $accessToken = ZohoCredential::query()->find(ZohoCredential::ACCESS_TOKEN);
        $this->assertNotNull($accessToken);
        $this->assertNotNull($accessToken->expires_at);
        $this->assertTrue($accessToken->expires_at->greaterThan(CarbonImmutable::now()->addMinutes(58)));
        $this->assertTrue($accessToken->expires_at->lessThanOrEqualTo(CarbonImmutable::now()->addMinutes(60)));

        $storedAccessToken = DB::table('zoho_credentials')
            ->where('key', ZohoCredential::ACCESS_TOKEN)
            ->value('value');
        $storedRefreshToken = DB::table('zoho_credentials')
            ->where('key', ZohoCredential::REFRESH_TOKEN)
            ->value('value');

        $this->assertIsString($storedAccessToken);
        $this->assertIsString($storedRefreshToken);
        $this->assertNotSame('oauth-access-token', $storedAccessToken);
        $this->assertNotSame('oauth-refresh-token', $storedRefreshToken);
    }

    public function test_callback_rejects_missing_authorisation_code(): void
    {
        Http::fake();

        $state = $this->authorizeAndExtractState();

        $response = $this->get(route('admin.zoho.callback', ['state' => $state]));

        $response->assertRedirect(ZohoConnection::getUrl());
        $response->assertSessionHas('zoho_connection_message', 'Zoho did not return a valid authorisation code.');
        $response->assertSessionHas('zoho_connection_status', 'danger');

        Http::assertNothingSent();
    }

    public function test_callback_rejects_an_expired_state(): void
    {
        Http::fake();

        $state = $this->authorizeAndExtractState();

        $this->travel(11)->minutes();

        $response = $this->get(route('admin.zoho.callback', [
            'state' => $state,
            'code' => 'oauth-authorisation-code',
        ]));

        $response->assertRedirect(ZohoConnection::getUrl());
        $response->assertSessionHas('zoho_connection_message', 'The Zoho authorisation state was invalid or expired.');
        $response->assertSessionHas('zoho_connection_status', 'danger');

        Http::assertNothingSent();
    }

    public function test_callback_handles_failed_token_exchange(): void
    {
        Http::fake([
            'https://accounts.zoho.test/oauth/v2/token' => Http::response(['message' => 'Server Error'], 500),
        ]);

        $state = $this->authorizeAndExtractState();

        $response = $this->get(route('admin.zoho.callback', [
            'state' => $state,
            'code' => 'oauth-authorisation-code',
        ]));

        $response->assertRedirect(ZohoConnection::getUrl());
        $response->assertSessionHas('zoho_connection_message', 'Zoho authorisation failed during the token exchange.');
        $response->assertSessionHas('zoho_connection_status', 'danger');

        $this->assertNull(ZohoCredential::get(ZohoCredential::ACCESS_TOKEN));
        $this->assertNull(ZohoCredential::get(ZohoCredential::REFRESH_TOKEN));
    }

    public function test_callback_requires_both_tokens_in_the_exchange_response(): void
    {
        Http::fake([
            'https://accounts.zoho.test/oauth/v2/token' => Http::response([
                'access_token' => 'oauth-access-token',
                'expires_in' => 3600,
            ], 200),
        ]);

        $state = $this->authorizeAndExtractState();

        $response = $this->get(route('admin.zoho.callback', [
            'state' => $state,
            'code' => 'oauth-authorisation-code',
        ]));

        $response->assertRedirect(ZohoConnection::getUrl());
        $response->assertSessionHas(
            'zoho_connection_message',
            'Zoho did not return both an access token and refresh token.',
        );
        $response->assertSessionHas('zoho_connection_status', 'danger');

        $this->assertNull(ZohoCredential::get(ZohoCredential::ACCESS_TOKEN));
        $this->assertNull(ZohoCredential::get(ZohoCredential::REFRESH_TOKEN));
    }

    private function authorizeAndExtractState(): string
    {
        $response = $this->get(route('admin.zoho.authorize'));

        $response->assertRedirect();

        $location = $response->headers->get('Location');
        $this->assertIsString($location);

        $queryString = parse_url($location, PHP_URL_QUERY);
        $this->assertIsString($queryString);

        $query = [];
        parse_str($queryString, $query);

        $state = $query['state'] ?? null;
        $this->assertIsString($state);

        return $state;
    }

    private function storeConnectionSettings(): void
    {
        ZohoCredential::set(ZohoCredential::CLIENT_ID, 'test-client-id');
        ZohoCredential::set(ZohoCredential::CLIENT_SECRET, 'test-client-secret');
        ZohoCredential::set(ZohoCredential::API_DOMAIN, 'https://www.zohoapis.test');
        ZohoCredential::set(ZohoCredential::ACCOUNTS_URL, 'https://accounts.zoho.test');
        ZohoCredential::set(ZohoCredential::AUTHORIZATION_URL, 'https://accounts.zoho.test/oauth/v2/auth');
        ZohoCredential::set(ZohoCredential::ACCESS_TOKEN_URL, 'https://accounts.zoho.test/oauth/v2/token');
        ZohoCredential::set(ZohoCredential::SCOPE, 'ZohoCRM.modules.ALL');
    }
}
