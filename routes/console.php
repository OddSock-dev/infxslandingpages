<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('zoho:refresh-token', function () {
    $client = app(\App\Integrations\Zoho\ZohoCrmClient::class);

    try {
        $client->refreshToken();
        $this->info('Zoho access token refreshed successfully.');
    } catch (\Throwable $e) {
        $this->error("Zoho token refresh failed: {$e->getMessage()}");

        return 1;
    }
})->purpose('Proactively refresh the Zoho CRM access token');
