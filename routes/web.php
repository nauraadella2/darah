<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PetugasController; 
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


// Langsung arahkan root URL ke halaman login
Route::get('/', [AuthenticatedSessionController::class, 'create'])->name('login');

// Route login & logout
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');


// Route untuk Admin
Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/permintaan', [AdminController::class, 'permintaan'])->name('permintaan');
    Route::get('/prediksi', [AdminController::class, 'prediksi'])->name('prediksi');
    Route::get('/optimasi', [AdminController::class, 'optimasi'])->name('optimasi');
    Route::get('/pengujian', [AdminController::class, 'pengujian'])->name('pengujian');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/input', [AdminController::class, 'input'])->name('input');
});

// Route untuk Petugas
Route::middleware(['role:petugas'])->prefix('petugas')->name('petugas.')->group(function () {
    Route::get('/dashboard', [PetugasController::class, 'dashboard'])->name('dashboard');
    Route::get('/permintaan', [PetugasController::class, 'permintaan'])->name('permintaan');
    Route::get('/prediksi', [PetugasController::class, 'prediksi'])->name('prediksi');
});

require __DIR__.'/auth.php';
