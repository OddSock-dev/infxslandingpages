<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Services\AnalyticsService;
use Tests\TestCase;

class AnalyticsServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        $this->unsetRuntimeAnalyticsEnvironment();

        parent::tearDown();
    }

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

    public function test_it_can_fall_back_to_runtime_environment_values(): void
    {
        config()->set('services.analytics.enabled', false);
        config()->set('services.analytics.ga4_measurement_id', null);
        config()->set('services.analytics.measurement_id', null);

        $this->setRuntimeAnalyticsEnvironment('ANALYTICS_ENABLED', 'true');
        $this->setRuntimeAnalyticsEnvironment('GA4_MEASUREMENT_ID', 'G-RUNTIME123');

        $this->assertTrue(AnalyticsService::isEnabled());
        $this->assertSame('G-RUNTIME123', AnalyticsService::ga4MeasurementId());
        $this->assertStringContainsString(
            'https://www.googletagmanager.com/gtag/js?id=G-RUNTIME123',
            view('components.analytics.ga4-script')->render(),
        );
    }

    private function setRuntimeAnalyticsEnvironment(string $key, string $value): void
    {
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
        putenv("{$key}={$value}");
    }

    private function unsetRuntimeAnalyticsEnvironment(): void
    {
        foreach ([
            'ANALYTICS_ENABLED',
            'GA4_MEASUREMENT_ID',
            'ANALYTICS_GA4_MEASUREMENT_ID',
            'ANALYTICS_MEASUREMENT_ID',
        ] as $key) {
            unset($_ENV[$key], $_SERVER[$key]);
            putenv($key);
        }
    }
}
