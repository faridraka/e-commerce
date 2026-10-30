<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\ProductController;
use App\Http\Middleware\EnsureOnboardingCompleted;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home')->middleware(EnsureOnboardingCompleted::class);

// Katalog & detail produk (publik)
Route::get('katalog', [ProductController::class, 'index'])->name('products.index');
Route::get('produk/{product:slug}', [ProductController::class, 'show'])->name('products.show');

// Halaman untuk tamu (belum login)
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisterController::class, 'create'])->name('register');
    Route::post('register', [RegisterController::class, 'store'])->name('register.store');

    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store'])->name('login.store');
});

Route::post('logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// Onboarding (wajib login)
Route::middleware('auth')->group(function () {
    Route::get('onboarding', [OnboardingController::class, 'showRole'])->name('onboarding.role');
    Route::post('onboarding', [OnboardingController::class, 'storeRole'])->name('onboarding.role.store');

    Route::get('onboarding/verifikasi', [OnboardingController::class, 'showVerification'])->name('onboarding.verification');
    Route::post('onboarding/verifikasi', [OnboardingController::class, 'verify'])->name('onboarding.verification.store');
    Route::post('onboarding/verifikasi/lewati', [OnboardingController::class, 'skip'])->name('onboarding.verification.skip');
});

// Endpoint JSON publik
Route::prefix('api')->group(function () {
    Route::get('products', [ProductController::class, 'apiIndex'])->name('api.products.index');
    Route::get('products/{slug}', [ProductController::class, 'apiShow'])->name('api.products.show');
});

// Keranjang (khusus user login; tamu diarahkan ke halaman daftar)
Route::post('keranjang/{product}', [CartController::class, 'store'])
    ->middleware('auth')
    ->name('cart.store');
