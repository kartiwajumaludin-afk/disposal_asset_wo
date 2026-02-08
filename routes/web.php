<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\CsvBatchImportController;
use App\Livewire\CsvBatchUpload;

/*
|--------------------------------------------------------------------------
| CSV Batch Import Routes (Native - No Livewire)
|--------------------------------------------------------------------------
*/
Route::get('/csv-batch-import', function () {
    return view('csv-batch-import');
})->name('csv.batch.import');

Route::post('/csv-batch-import/process', [CsvBatchImportController::class, 'process'])
    ->name('csv.batch.import.process');

/*
|--------------------------------------------------------------------------
| CSV Import (Livewire) - OLD VERSION
|--------------------------------------------------------------------------
*/
Route::get('/import', CsvBatchUpload::class)->name('import.index');

/*
|--------------------------------------------------------------------------
| Asset Management Routes
|--------------------------------------------------------------------------
*/
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

/*
|--------------------------------------------------------------------------
| Redirect Root to Dashboard
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('dashboard');
});