<?php

declare(strict_types=1);

use App\Http\Controllers\PublicSiteController;
use App\Http\Controllers\TrialStartController;
use App\Http\Controllers\ZohoOAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicSiteController::class, 'landing'])->name('home');
Route::get('/robots.txt', [PublicSiteController::class, 'robots'])->name('robots');
Route::get('/sitemap.xml', [PublicSiteController::class, 'sitemap'])->name('sitemap');
Route::get('/products/{slug}', [PublicSiteController::class, 'product'])->name('products.show');
Route::get('/products/{pageKey}/start-trial', TrialStartController::class)->middleware('signed')->name('products.trial.start');
Route::get('/thanks', [PublicSiteController::class, 'thanks'])->name('thanks');
Route::get('/privacy', [PublicSiteController::class, 'privacy'])->name('privacy');
Route::get('/terms', [PublicSiteController::class, 'terms'])->name('terms');

Route::middleware(['auth', 'throttle:10,1'])
    ->prefix('admin/integrations/zoho')
    ->name('admin.zoho.')
    ->group(function (): void {
        Route::get('/authorize', [ZohoOAuthController::class, 'authorize'])->name('authorize');
        Route::get('/callback', [ZohoOAuthController::class, 'callback'])->name('callback');
    });
