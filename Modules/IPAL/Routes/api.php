<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| IPAL API Routes
|--------------------------------------------------------------------------
|
| Semua route API untuk module IPAL didefinisikan di sini.
| Route ini otomatis mendapatkan prefix 'api/ipal' dan name prefix 'api.ipal.'
|
*/

/** Statistics endpoint — public read access */
Route::get('/statistics', [\Modules\IPAL\Http\Controllers\Api\StatisticsController::class, 'index'])->name('statistics.index');

/** Manhole endpoints — public read access */
Route::get('/manholes/filters', [\Modules\IPAL\Http\Controllers\Api\ManholeController::class, 'filters'])->name('manholes.filters');
Route::get('/manholes/geojson', [\Modules\IPAL\Http\Controllers\Api\ManholeController::class, 'geojson'])->name('manholes.geojson');
Route::apiResource('manholes', \Modules\IPAL\Http\Controllers\Api\ManholeController::class)->only(['index', 'show']);

/** Pipe endpoints — public read access */
Route::get('/pipes/filters', [\Modules\IPAL\Http\Controllers\Api\PipeController::class, 'filters'])->name('pipes.filters');
Route::get('/pipes/geojson', [\Modules\IPAL\Http\Controllers\Api\PipeController::class, 'geojson'])->name('pipes.geojson');
Route::apiResource('pipes', \Modules\IPAL\Http\Controllers\Api\PipeController::class)->only(['index', 'show']);

/** Aduan submission — public access */
Route::post('/aduan', [\Modules\IPAL\Http\Controllers\Api\AduanController::class, 'store'])->name('aduan.store');

Route::middleware('auth:sanctum')->group(function () {

    /** Upload endpoints */
    Route::get('/uploads', [\Modules\IPAL\Http\Controllers\Api\UploadController::class, 'index'])->name('uploads.index');
    Route::get('/uploads/{id}', [\Modules\IPAL\Http\Controllers\Api\UploadController::class, 'show'])->name('uploads.show');
    Route::post('/upload', [\Modules\IPAL\Http\Controllers\Api\UploadController::class, 'store'])->name('uploads.store');
    Route::delete('/uploads/{id}', [\Modules\IPAL\Http\Controllers\Api\UploadController::class, 'destroy'])->name('uploads.destroy');

    /** Manhole update — protected write access */
    Route::apiResource('manholes', \Modules\IPAL\Http\Controllers\Api\ManholeController::class)->only(['update']);

    /** Pipe update — protected write access */
    Route::apiResource('pipes', \Modules\IPAL\Http\Controllers\Api\PipeController::class)->only(['update']);

    /** Aduan management — admin access */
    Route::get('/aduan', [\Modules\IPAL\Http\Controllers\Api\AduanController::class, 'index'])->name('aduan.index');
    Route::get('/aduan/{id}', [\Modules\IPAL\Http\Controllers\Api\AduanController::class, 'show'])->name('aduan.show');
    Route::put('/aduan/{id}/status', [\Modules\IPAL\Http\Controllers\Api\AduanController::class, 'updateStatus'])->name('aduan.updateStatus');
});
