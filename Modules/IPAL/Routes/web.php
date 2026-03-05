<?php

use Illuminate\Support\Facades\Route;
use Modules\IPAL\Http\Controllers\DashboardController;

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

    // ====================================================================
    // Tambahkan route IPAL lainnya di bawah ini
    // ====================================================================

    // Contoh:
    // Route::resource('pengolahan', PengolahanController::class);
    // Route::resource('pemantauan', PemantauanController::class);
    // Route::resource('laporan', LaporanController::class);
});
