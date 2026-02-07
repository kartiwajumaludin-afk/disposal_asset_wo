<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetController;

// Redirect halaman depan ke Dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Grup Route untuk Asset Management
Route::controller(AssetController::class)->group(function () {
    Route::get('/dashboard', 'dashboard')->name('dashboard');
    Route::get('/assets', 'index')->name('asset.index');
    Route::get('/assets/import', 'import')->name('asset.import');
    Route::get('/assets/tracker', 'tracker')->name('asset.tracker');
    Route::get('/assets/activity', 'activity')->name('asset.activity');
    Route::get('/assets/sitemap', 'sitemap')->name('asset.sitemap');
    Route::get('/assets/boq', 'boq')->name('asset.boq');
    Route::get('/assets/inbound', 'inbound')->name('asset.inbound');
});