<?php

declare(strict_types=1);

return [
    'enabled' => (bool) env('ANALYTICS_ENABLED', true),
    'ga4' => [
        'measurement_id' => env('GA4_MEASUREMENT_ID', ''),
    ],
];
