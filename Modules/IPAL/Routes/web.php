<?php

use Illuminate\Support\Facades\Route;
use Modules\IPAL\Http\Controllers\DashboardController;
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

Route::middleware(['auth', 'verified', 'checkRole:Super Admin,Admin'])->group(function () {
    // Dashboard IPAL
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /** Upload Data Jaringan */
    Route::get('/upload', [UploadController::class, 'index'])->name('upload.index');
    Route::post('/upload', [UploadController::class, 'store'])->name('upload.store');
    Route::delete('/upload/{id}', [UploadController::class, 'destroy'])->name('upload.destroy');
});
