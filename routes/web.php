<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\OnboardingController;
use App\Http\Middleware\EnsureOnboardingCompleted;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home')->middleware(EnsureOnboardingCompleted::class);

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
