<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Config;

class AnalyticsService
{
    public static function isEnabled(): bool
    {
        return Config::get('analytics.enabled') && filled(static::ga4MeasurementId());
    }

    public static function ga4MeasurementId(): string
    {
        $id = Config::get('analytics.ga4.measurement_id');

        return is_string($id) ? $id : '';
    }

    /**
     * Get GA4 configuration as a JavaScript object
     *
     * @return array<string, string|bool>
     */
    public static function ga4Config(): array
    {
        return [
            'measurement_id' => static::ga4MeasurementId(),
            'enabled' => static::isEnabled(),
        ];
    }
}
