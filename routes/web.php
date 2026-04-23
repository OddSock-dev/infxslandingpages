<?php

declare(strict_types=1);

use App\Http\Controllers\PublicSiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicSiteController::class, 'landing'])->name('home');
Route::get('/products/{slug}', [PublicSiteController::class, 'product'])->name('products.show');
Route::get('/thanks', [PublicSiteController::class, 'thanks'])->name('thanks');
Route::get('/privacy', [PublicSiteController::class, 'privacy'])->name('privacy');
Route::get('/terms', [PublicSiteController::class, 'terms'])->name('terms');
