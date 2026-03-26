<?php

use Illuminate\Support\Facades\Route;
use Modules\IPAL\Http\Controllers\DashboardController;
use Modules\IPAL\Http\Controllers\MapController;
use Modules\IPAL\Http\Controllers\UploadController;

/*
|--------------------------------------------------------------------------
| IPAL Web Routes
|--------------------------------------------------------------------------
|
| Semua route web untuk module IPAL didefinisikan di sini.
| Route ini otomatis mendapatkan prefix 'ipal' dan name prefix 'ipal.'
|
| Contoh akses: /ipal/dashboard -> ipal.dashboard.index
|
*/

// Peta publik — dapat diakses tanpa login
Route::get('/map', [MapController::class, 'index'])->name('map.index');
Route::get('/lapor-masalah', [MapController::class, 'laporMasalah'])->name('lapor-masalah.index');

Route::middleware(['auth', 'verified', 'checkRole:Super Admin,Admin'])->group(function () {
    // Dashboard IPAL
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /** Data Jaringan */
    Route::get('/upload', [UploadController::class, 'index'])->name('upload.index');
    Route::get('/upload/history', [UploadController::class, 'history'])->name('upload.history');
    Route::post('/upload', [UploadController::class, 'store'])->name('upload.store');
    Route::delete('/upload/{id}', [UploadController::class, 'destroy'])->name('upload.destroy');

    /** Manajemen Aduan */
    Route::get('/aduan', [\Modules\IPAL\Http\Controllers\AduanController::class, 'index'])->name('aduan.index');
    Route::get('/aduan/{id}', [\Modules\IPAL\Http\Controllers\AduanController::class, 'show'])->name('aduan.show');
    Route::post('/aduan/{id}/status', [\Modules\IPAL\Http\Controllers\AduanController::class, 'updateStatus'])->name('aduan.updateStatus');
});
