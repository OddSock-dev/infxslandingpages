<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Support\CrmPayloadRedactor;
use PHPUnit\Framework\TestCase;

class CrmPayloadRedactorTest extends TestCase
{
    public function test_it_redacts_sensitive_fields_in_nested_payloads(): void
    {
        $payload = [
            'data' => [[
                'Email' => 'jane@example.com',
                'Phone' => '+27 82 555 1212',
                'First_Name' => 'Jane',
                'Company' => 'Acme Pty Ltd',
            ]],
            'access_token' => 'abcdefghijklmnopqrstuvwxyz123456',
        ];

        $redacted = CrmPayloadRedactor::flattenForDisplay($payload);

        $this->assertSame('j***@example.com', $redacted['data.0.Email']);
        $this->assertSame('*******1212', $redacted['data.0.Phone']);
        $this->assertSame('J***', $redacted['data.0.First_Name']);
        $this->assertSame('A*** P*** L***', $redacted['data.0.Company']);
        $this->assertSame('[redacted]', $redacted['access_token']);
    }

    public function test_it_redacts_sensitive_strings_inside_freeform_messages(): void
    {
        $message = 'Lead jane@example.com called from +27 82 555 1212 with token abcdefghijklmnopqrstuvwxyz123456.';

        $redacted = CrmPayloadRedactor::redactMessage($message);

        $this->assertNotNull($redacted);
        $this->assertStringNotContainsString('jane@example.com', $redacted);
        $this->assertStringNotContainsString('+27 82 555 1212', $redacted);
        $this->assertStringNotContainsString('abcdefghijklmnopqrstuvwxyz123456', $redacted);
        $this->assertStringContainsString('j***@example.com', $redacted);
        $this->assertStringContainsString('*******1212', $redacted);
        $this->assertStringContainsString('[redacted]', $redacted);
    }
}
