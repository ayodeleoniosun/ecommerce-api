<?php

use App\Domain\Admin\Controllers\CategoryController as AdminCategoryController;
use App\Domain\Admin\Controllers\RoleController;
use App\Domain\Auth\Controllers\AuthController;
use App\Domain\Inventory\Controllers\CategoryController;
use App\Domain\Inventory\Controllers\ProductController;
use App\Domain\Order\Controllers\CartController;
use App\Domain\Order\Controllers\OrderController;
use App\Domain\Order\Controllers\WishlistController;
use App\Domain\Shipping\Controllers\PickupStationController;
use App\Domain\Shipping\Controllers\ShippingAddressController;
use App\Domain\Vendor\Onboarding\Controllers\OnboardingController;
use App\Domain\Vendor\Products\Controllers\ProductController as VendorProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('token/verify', [AuthController::class, 'verifyToken']);
    Route::post('token/resend', [AuthController::class, 'resendToken']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->name('password.reset');
    Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::prefix('inventory')->group(function () {
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index']);
        Route::get('/{productUUID}', [ProductController::class, 'view']);
    });

    Route::prefix('categories')->group(function () {
        Route::get('', [CategoryController::class, 'index']);

        Route::prefix('variations')->group(function () {
            Route::get('/{categoryUUID}', [CategoryController::class, 'getCategoryVariations']);
            Route::get('/{variationUUID}/options', [CategoryController::class, 'getCategoryVariationOptions']);
        });
    });
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('authenticated', [AuthController::class, 'authenticated']);

    Route::prefix('addresses')->group(function () {
        Route::post('/', [ShippingAddressController::class, 'store']);
        Route::get('/', [ShippingAddressController::class, 'index']);
    });

    Route::prefix('orders')->group(function () {
        Route::get('/{currency}', [OrderController::class, 'index']);
        Route::get('/view/{id}', [OrderController::class, 'view']);
        Route::post('/', [OrderController::class, 'store']);
        Route::post('/pay', [OrderController::class, 'pay']);
        Route::post('/pay/authorize', [OrderController::class, 'authorize']);
    });

    Route::prefix('wishlists')->group(function () {
        Route::get('/', [WishlistController::class, 'index']);
        Route::post('/', [WishlistController::class, 'store']);
        Route::post('/{id}/cart', [WishlistController::class, 'addToCart']);
        Route::delete('/{id}', [WishlistController::class, 'delete']);
    });

    Route::prefix('carts')->group(function () {
        Route::get('/', [CartController::class, 'index']);
        Route::post('/', [CartController::class, 'store']);
        Route::delete('/{cartItemUUID}', [CartController::class, 'delete']);
    });

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

        Route::prefix('pickup-stations')->group(function () {
            Route::get('', [PickupStationController::class, 'index']);
            Route::get('/{stationUUID}', [PickupStationController::class, 'view']);
            Route::post('', [PickupStationController::class, 'store']);
            Route::post('/{stationUUID}/opening-hours', [PickupStationController::class, 'storeOpeningHours']);

            //            Route::put('/{stationUUID}', [PickupStationController::class, 'update']);
            //            Route::put('/{stationUUID}/opening-hours', [PickupStationController::class, 'updateOpeningHours']);
            //
            //            Route::delete('/{stationUUID}', [PickupStationController::class, 'delete']);
            //            Route::delete('/{stationUUID}/opening-hours', [PickupStationController::class, 'deleteOpeningHours']);
        });
    });

    Route::prefix('vendors')->group(function () {
        Route::prefix('setup')->group(function () {
            Route::get('status', [OnboardingController::class, 'status']);
            Route::post('contact', [OnboardingController::class, 'contact']);
            Route::post('business', [OnboardingController::class, 'business']);
            Route::post('legal', [OnboardingController::class, 'legal']);
            Route::post('payment', [OnboardingController::class, 'payment']);
        });

        Route::prefix('products')->group(function () {
            Route::get('', [VendorProductController::class, 'index']);
            Route::get('/{productUUID}', [VendorProductController::class, 'view']);

            Route::post('', [VendorProductController::class, 'storeOrUpdateProduct']);
            Route::post('/items', [VendorProductController::class, 'storeOrUpdateProductItems']);
            Route::post('/images', [VendorProductController::class, 'storeImages']);

            Route::delete('/images/{productImageUUID}', [VendorProductController::class, 'deleteProductImage']);
            Route::delete('/items/{productItemUUID}', [VendorProductController::class, 'deleteProductItem']);
            Route::delete('/{productUUID}', [VendorProductController::class, 'deleteProduct']);
        });
    });
});
