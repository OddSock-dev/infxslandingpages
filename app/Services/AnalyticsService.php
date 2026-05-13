<?php

declare(strict_types=1);

namespace App\Services;

final class AnalyticsService
{
    public static function isEnabled(): bool
    {
        return (self::environmentEnabled() ?? self::configEnabled()) && self::ga4MeasurementId() !== null;
    }

    public static function ga4MeasurementId(): ?string
    {
        foreach ([
            self::environmentMeasurementId(),
            self::configMeasurementId(),
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

    private static function configMeasurementId(): ?string
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

    private static function configEnabled(): bool
    {
        $enabled = config('services.analytics.enabled', false);

        if (! is_string($enabled) && ! is_int($enabled) && ! is_bool($enabled) && $enabled !== null) {
            return false;
        }

        return self::normalizeBoolean($enabled);
    }

    private static function environmentMeasurementId(): ?string
    {
        foreach ([
            'GA4_MEASUREMENT_ID',
            'ANALYTICS_GA4_MEASUREMENT_ID',
            'ANALYTICS_MEASUREMENT_ID',
        ] as $key) {
            $measurementId = self::environmentValue($key);

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

    private static function environmentEnabled(): ?bool
    {
        $enabled = self::environmentValue('ANALYTICS_ENABLED');

        if ($enabled === null) {
            return null;
        }

        return self::normalizeBoolean($enabled);
    }

    private static function environmentValue(string $key): string|int|bool|null
    {
        foreach ([
            $_ENV[$key] ?? null,
            $_SERVER[$key] ?? null,
            getenv($key),
        ] as $value) {
            if ($value === null || $value === false) {
                continue;
            }

            if (is_string($value) || is_int($value) || is_bool($value)) {
                return $value;
            }
        }

        return null;
    }

    private static function normalizeBoolean(string|int|bool|null $enabled): bool
    {
        return match (true) {
            is_bool($enabled) => $enabled,
            is_int($enabled) => $enabled === 1,
            is_string($enabled) => filter_var($enabled, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? false,
            default => false,
        };
    }
}
