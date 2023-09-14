<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PembayaranController;
use App\Http\Controllers\Admin\ProdukController;
use App\Http\Controllers\Pengguna\BerandaController;
use App\Http\Controllers\Admin\JenisStatusController;
use App\Http\Controllers\Admin\KelolaAdminController;
use App\Http\Controllers\Admin\KonfigurasiController;
use App\Http\Controllers\Admin\KelolaBannerController;
use App\Http\Controllers\Pengguna\PengajuanController;
use App\Http\Controllers\Pengguna\ProdukUjiController;
use App\Http\Controllers\Pengguna\StatusUjiController;
use App\Http\Controllers\Admin\JenisPengujianController;
use App\Http\Controllers\Admin\KelolaPenggunaController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\Admin\StatusPengajuanController;

use App\Http\Controllers\Admin\ParameterPengujianController;
use App\Http\Controllers\Admin\RiwayatStatusPengujianController;
use App\Http\Controllers\Pengguna\CetakPermintaanPengujianController;
use App\Http\Controllers\Admin\PengajuanByAdminController;

//landing

Route::get('/', [LandingController::class, 'index']);
Route::get('lacak/{kode_sampel}', [LandingController::class, 'lacak']);

//dashboard
Route::resource('dashboard', DashboardController::class)->middleware(['checkRole:Super Admin,Admin,Pengguna', 'auth', 'verified']);

//fitur
Route::resource('pengajuan_by_admin', PengajuanByAdminController::class)->middleware(['checkRole:Super Admin,Admin', 'auth', 'verified']);
Route::resource('pengajuan_uji', PembayaranController::class)->middleware(['checkRole:Super Admin,Admin', 'auth', 'verified']);
Route::resource('admin_produk_uji', ProdukController::class)->middleware(['checkRole:Super Admin,Admin', 'auth', 'verified']);
Route::get('status_pengujian/export/', [StatusPengajuanController::class, 'export'])
    ->name('status_pengujian.export');
Route::resource('status_pengujian', StatusPengajuanController::class)->middleware(['checkRole:Super Admin,Admin', 'auth', 'verified']);
Route::resource('riwayat_status_pengujian', RiwayatStatusPengujianController::class)->middleware(['checkRole:Super Admin,Admin', 'auth', 'verified']);

//master data
Route::resource('kelola_banner', KelolaBannerController::class)->middleware(['checkRole:Super Admin,Admin', 'auth', 'verified']);
Route::resource('kelola_jenis_pengujian', JenisPengujianController::class)->middleware(['checkRole:Super Admin,Admin', 'auth', 'verified']);
Route::resource('kelola_parameter_uji', ParameterPengujianController::class)->middleware(['checkRole:Super Admin,Admin', 'auth', 'verified']);
Route::resource('kelola_jenis_status', JenisStatusController::class)->middleware(['checkRole:Super Admin,Admin', 'auth', 'verified']);

//setting
Route::resource('kelola_pengguna', KelolaPenggunaController::class)->middleware(['checkRole:Super Admin', 'auth', 'verified']);
Route::resource('kelola_admin', KelolaAdminController::class)->middleware(['checkRole:Super Admin', 'auth', 'verified']);
Route::resource('konfigurasi', KonfigurasiController::class)->middleware(['checkRole:Super Admin', 'auth', 'verified']);

//Pengguna 
Route::resource('pengajuan', PengajuanController::class);
Route::resource('beranda', BerandaController::class)->middleware(['checkRole:Pengguna']);
Route::resource('status_uji', StatusUjiController::class)->middleware(['checkRole:Pengguna']);
Route::resource('produk_uji', ProdukUjiController::class)->middleware(['checkRole:Pengguna']);
Route::resource('cetak_permintaan_pengujian', CetakPermintaanPengujianController::class);

//Profil
Route::resource('profil', ProfilController::class);

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
