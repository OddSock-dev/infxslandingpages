<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\ZohoCredential;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ZohoCredentialTest extends TestCase
{
    use RefreshDatabase;

    public function test_values_are_encrypted_at_rest(): void
    {
        ZohoCredential::set(ZohoCredential::CLIENT_SECRET, 'plain-client-secret');

        $this->assertSame('plain-client-secret', ZohoCredential::get(ZohoCredential::CLIENT_SECRET));

        $storedValue = DB::table('zoho_credentials')
            ->where('key', ZohoCredential::CLIENT_SECRET)
            ->value('value');

        $this->assertIsString($storedValue);
        $this->assertNotSame('plain-client-secret', $storedValue);
        $this->assertSame('plain-client-secret', Crypt::decryptString($storedValue));
    }

    public function test_is_expired_reflects_missing_past_and_future_credentials(): void
    {
        $this->assertTrue(ZohoCredential::isExpired(ZohoCredential::ACCESS_TOKEN));

        ZohoCredential::set(
            ZohoCredential::ACCESS_TOKEN,
            'expired-token',
            CarbonImmutable::now()->subMinute(),
        );

        $this->assertTrue(ZohoCredential::isExpired(ZohoCredential::ACCESS_TOKEN));

        ZohoCredential::set(
            ZohoCredential::ACCESS_TOKEN,
            'fresh-token',
            CarbonImmutable::now()->addMinute(),
        );

        $this->assertFalse(ZohoCredential::isExpired(ZohoCredential::ACCESS_TOKEN));
    }
}
