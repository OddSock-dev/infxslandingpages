<?php

declare(strict_types=1);

use App\Http\Controllers\Api\FunnelController;
use App\Http\Controllers\Api\JourneyController;
use App\Http\Controllers\Api\PageConfigController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Compatibility endpoints for integrations or alternate clients.
| The public Blade + Livewire marketing flow persists directly through
| Laravel models and services without using these routes.
|
*/

Route::middleware('throttle:10,1')->group(function (): void {
    Route::post('/funnel/qualify', [FunnelController::class, 'qualify']);
    Route::post('/funnel/submit', [FunnelController::class, 'submit']);
});
Route::get('/journeys/{token}/prefill', [JourneyController::class, 'prefill']);
Route::get('/config/pages/{pageKey}', [PageConfigController::class, 'show']);
