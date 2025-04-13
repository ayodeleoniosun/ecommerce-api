<?php

use App\Application\Http\Auth\Controllers\AuthController;
use App\Application\Http\Onboarding\Controllers\OnboardingController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('token/verify', [AuthController::class, 'verifyToken']);
    Route::post('token/resend', [AuthController::class, 'resendToken']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->name('password.reset');
    Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::prefix('seller')->group(function () {
        Route::prefix('setup')->group(function () {
            Route::get('status', [OnboardingController::class, 'status']);
            Route::post('contact', [OnboardingController::class, 'contact']);
            Route::post('business', [OnboardingController::class, 'business']);
            Route::post('legal', [OnboardingController::class, 'legal']);
            Route::post('payment', [OnboardingController::class, 'payment']);
        });
    });
});
