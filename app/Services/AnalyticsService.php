<?php

declare(strict_types=1);

namespace App\Services;

final class AnalyticsService
{
    public static function isEnabled(): bool
    {
        return self::analyticsEnabled() && self::ga4MeasurementId() !== null;
    }

    public static function ga4MeasurementId(): ?string
    {
        foreach ([
            config('services.analytics.ga4_measurement_id'),
            config('services.analytics.measurement_id'),
        ] as $measurementId) {
            if (! is_string($measurementId)) {
                continue;
            }

            $trimmedMeasurementId = trim($measurementId);

            if ($trimmedMeasurementId !== '') {
                return $trimmedMeasurementId;
            }
        }

        return null;
    }

    private static function analyticsEnabled(): bool
    {
        $enabled = config('services.analytics.enabled', false);

        return match (true) {
            is_bool($enabled) => $enabled,
            is_int($enabled) => $enabled === 1,
            is_string($enabled) => filter_var($enabled, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? false,
            default => false,
        };
    }
}
