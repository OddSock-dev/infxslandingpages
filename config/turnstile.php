<?php

declare(strict_types=1);

return [
    'enabled' => (bool) env('TURNSTILE_ENABLED', true),
    'site_key' => env('TURNSTILE_SITE_KEY', ''),
    'secret_key' => env('TURNSTILE_SECRET_KEY', ''),
    'verify_endpoint' => 'https://challenges.cloudflare.com/turnstile/v0/siteverify',
    'timeout' => 10,
];
