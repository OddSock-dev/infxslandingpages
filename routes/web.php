<?php

use Illuminate\Support\Facades\Route;

// Public web routes are handled by the Nuxt frontend (zoho.infxsolutions.co.za).
// This Laravel app serves the API (/api/*) and Filament admin (/admin).
// The health check route is registered by the application bootstrap.
Route::get('/', fn () => redirect('/admin'))->name('home');
