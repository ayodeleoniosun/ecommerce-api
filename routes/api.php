<?php

use App\Domain\Admin\Controllers\RoleController;
use App\Domain\Auth\Controllers\AuthController;
use App\Domain\Inventory\Controllers\CategoryController;
use App\Domain\Onboarding\Controllers\OnboardingController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('token/verify', [AuthController::class, 'verifyToken']);
    Route::post('token/resend', [AuthController::class, 'resendToken']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->name('password.reset');
    Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::prefix('catalogues')->group(function () {
    Route::get('categories', [CategoryController::class, 'index']);
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('authenticated', [AuthController::class, 'authenticated']);

    Route::prefix('admin')->group(function () {
        Route::get('roles', [RoleController::class, 'roles']);
        Route::get('permissions', [RoleController::class, 'permissions']);

        Route::prefix('users')->group(function () {
            Route::prefix('roles')->group(function () {
                Route::post('assign', [RoleController::class, 'assignRoles']);
                Route::post('revoke', [RoleController::class, 'revokeRole']);
            });
        });
    });

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
