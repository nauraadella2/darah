<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\PermintaanDarahController;
use App\Http\Controllers\OptimizationController;
use App\Http\Controllers\PredictionController;
use App\Http\Controllers\PengujianController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


// halaman login
Route::get('/', [AuthenticatedSessionController::class, 'create'])->name('login');

// Route login & logout
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');


// Route untuk Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/permintaan', [AdminController::class, 'permintaan'])->name('permintaan');
    Route::get('/prediksi', [AdminController::class, 'prediksi'])->name('prediksi');
    Route::get('/optimasi', [AdminController::class, 'optimasi'])->name('optimasi');
    Route::get('/pengujian', [AdminController::class, 'pengujian'])->name('pengujian');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/input', [AdminController::class, 'input'])->name('input');
    Route::post('/input', [PermintaanDarahController::class, 'store'])->name('store');

    Route::post('/permintaan/input', [PermintaanDarahController::class, 'create'])->name('permintaan.input');
    Route::post('/permintaan/store', [PermintaanDarahController::class, 'store'])->name('permintaan.store');

    Route::get('/optimasi', [OptimizationController::class, 'index'])->name('optimasi');
    Route::post('/optimasi/hitung', [OptimizationController::class, 'hitungAlpha'])->name('optimasi.hitung');

    Route::get('/prediksi', [PredictionController::class, 'index'])->name('prediksi.index');
    Route::post('/prediksi/hitung', [PredictionController::class, 'hitungPrediksi'])->name('prediksi.hitung');
    Route::delete('/prediksi/hapus/{id}', [PredictionController::class, 'destroy'])->name('prediksi.hapus');

    Route::get('/pengujian', [PengujianController::class, 'index'])->name('pengujian.index');
    Route::post('/pengujian/proses', [PengujianController::class, 'proses'])->name('pengujian.proses');
    Route::get('/pengujian/filter', [PengujianController::class, 'filter'])->name('pengujian.filter');

});



// Route untuk Petugas
Route::middleware(['role:petugas'])->prefix('petugas')->name('petugas.')->group(function () {
    Route::get('/dashboard', [PetugasController::class, 'dashboard'])->name('dashboard');
    Route::get('/permintaan', [PetugasController::class, 'permintaan'])->name('permintaan');
    Route::get('/prediksi', [PetugasController::class, 'prediksi'])->name('prediksi');
});



require __DIR__ . '/auth.php';
