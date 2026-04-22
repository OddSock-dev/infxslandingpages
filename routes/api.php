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
| Funnel API endpoints for the Nuxt public frontend.
| All routes are prefixed with /api automatically by the router.
|
*/

Route::post('/funnel/qualify', [FunnelController::class, 'qualify']);
Route::post('/funnel/submit', [FunnelController::class, 'submit']);
Route::get('/journeys/{token}/prefill', [JourneyController::class, 'prefill']);
Route::get('/config/pages/{pageKey}', [PageConfigController::class, 'show']);
