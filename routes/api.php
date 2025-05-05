<?php

use App\Domain\Admin\Controllers\CategoryController as AdminCategoryController;
use App\Domain\Admin\Controllers\RoleController;
use App\Domain\Auth\Controllers\AuthController;
use App\Domain\Inventory\Controllers\CategoryController;
use App\Domain\Vendor\Onboarding\Controllers\OnboardingController;
use App\Domain\Vendor\Products\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('token/verify', [AuthController::class, 'verifyToken']);
    Route::post('token/resend', [AuthController::class, 'resendToken']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->name('password.reset');
    Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::prefix('categories')->group(function () {
    Route::get('', [CategoryController::class, 'index']);

    Route::prefix('variations')->group(function () {
        Route::get('/{categoryUUID}', [CategoryController::class, 'getCategoryVariations']);
        Route::get('/{variationUUID}/options', [CategoryController::class, 'getCategoryVariationOptions']);
    });
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

            Route::prefix('permissions')->group(function () {
                Route::post('assign', [RoleController::class, 'assignPermissions']);
                Route::post('revoke', [RoleController::class, 'revokePermission']);
            });
        });

        Route::prefix('categories')->group(function () {
            Route::prefix('variations')->group(function () {
                Route::post('', [AdminCategoryController::class, 'storeCategoryVariations']);
                Route::post('/options', [AdminCategoryController::class, 'storeCategoryVariationOptions']);
                Route::delete('/{variationUUID}', [AdminCategoryController::class, 'deleteCategoryVariations']);
                Route::delete('/options/{optionUUID}',
                    [AdminCategoryController::class, 'deleteCategoryVariationOptions']);
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

        Route::prefix('products')->group(function () {
            //            Route::get('', [ProductController::class, 'index']);
            //            Route::get('/{id}', [ProductController::class, 'view']);
            Route::post('', [ProductController::class, 'store']);
            Route::post('/items', [ProductController::class, 'storeItems']);
            //            Route::post('/{id}', [ProductController::class, 'storeImages']);
            //            Route::post('/configurations/{id}', [ProductController::class, 'storeConfigurations']);
            //            Route::put('/{id}', [ProductController::class, 'update']);
            //            Route::delete('/{id}', [ProductController::class, 'delete']);
        });
    });
});
