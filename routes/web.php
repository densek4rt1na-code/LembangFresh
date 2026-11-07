<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes - LembangFresh
|--------------------------------------------------------------------------
| Seluruh rute menggunakan named routes sesuai spesifikasi tugas.
*/

// Halaman Publik
Route::get('/', [PublicController::class, 'index'])->name('home');
Route::get('/product/{id}', [PublicController::class, 'showProduct'])->name('products.show');
Route::get('/gis', [PublicController::class, 'gisView'])->name('gis.index');
Route::get('/matching', [PublicController::class, 'matchingView'])->name('matching.index');

// Rute Autentikasi (Tamu / Belum Login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// Rute Terproteksi (Wajib Login / Middleware Auth)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [DashboardController::class, 'profile'])->name('profile');
    Route::post('/profile', [DashboardController::class, 'updateProfile'])->name('profile.update');
    
    // Fitur Khusus Berdasarkan Role
    Route::post('/dashboard/farmer-delivery', [DashboardController::class, 'updateFarmerDelivery'])->name('farmer.delivery.update');
    Route::post('/dashboard/mitra-availability', [DashboardController::class, 'toggleMitraAvailability'])->name('mitra.availability.toggle');
    Route::post('/dashboard/user/{id}/verify', [DashboardController::class, 'toggleVerification'])->name('admin.user.verify');
    
    // Pesanan & Permintaan
    Route::post('/order/{id}', [PublicController::class, 'orderProduct'])->name('order.create');
    Route::post('/order/{id}/status', [DashboardController::class, 'updateOrderStatus'])->name('order.status.update');
    Route::post('/demands', [PublicController::class, 'storeDemand'])->name('demands.store');
});
