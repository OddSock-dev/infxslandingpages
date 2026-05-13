<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Services\AnalyticsService;
use Tests\TestCase;

class AnalyticsServiceTest extends TestCase
{
    public function test_it_is_disabled_without_a_measurement_id(): void
    {
        config()->set('services.analytics.enabled', true);
        config()->set('services.analytics.ga4_measurement_id', null);
        config()->set('services.analytics.measurement_id', null);

        $this->assertFalse(AnalyticsService::isEnabled());
        $this->assertNull(AnalyticsService::ga4MeasurementId());
    }

    public function test_it_returns_a_trimmed_measurement_id_when_enabled(): void
    {
        config()->set('services.analytics.enabled', true);
        config()->set('services.analytics.ga4_measurement_id', '  G-TEST1234  ');
        config()->set('services.analytics.measurement_id', null);

        $this->assertTrue(AnalyticsService::isEnabled());
        $this->assertSame('G-TEST1234', AnalyticsService::ga4MeasurementId());
    }

    public function test_it_can_use_the_legacy_measurement_id_key(): void
    {
        config()->set('services.analytics.enabled', true);
        config()->set('services.analytics.ga4_measurement_id', null);
        config()->set('services.analytics.measurement_id', 'G-LEGACY123');

        $this->assertTrue(AnalyticsService::isEnabled());
        $this->assertSame('G-LEGACY123', AnalyticsService::ga4MeasurementId());
    }
}
