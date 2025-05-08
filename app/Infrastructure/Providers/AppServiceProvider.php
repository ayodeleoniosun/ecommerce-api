<?php

namespace App\Infrastructure\Providers;

use App\Domain\Admin\Interfaces\CategoryVariationOptionRepositoryInterface;
use App\Domain\Admin\Interfaces\CategoryVariationRepositoryInterface;
use App\Domain\Auth\Interfaces\Repositories\UserRepositoryInterface;
use App\Domain\Auth\Interfaces\Repositories\UserVerificationRepositoryInterface;
use App\Domain\Inventory\Interfaces\CategoryRepositoryInterface;
use App\Domain\Vendor\Onboarding\Interfaces\VendorBusinessInformationRepositoryInterface;
use App\Domain\Vendor\Onboarding\Interfaces\VendorContactInformationRepositoryInterface;
use App\Domain\Vendor\Onboarding\Interfaces\VendorLegalInformationRepositoryInterface;
use App\Domain\Vendor\Onboarding\Interfaces\VendorPaymentInformationRepositoryInterface;
use App\Domain\Vendor\Products\Interfaces\ProductImageRepositoryInterface;
use App\Domain\Vendor\Products\Interfaces\ProductItemRepositoryInterface;
use App\Domain\Vendor\Products\Interfaces\ProductRepositoryInterface;
use App\Infrastructure\Repositories\Auth\UserRepository;
use App\Infrastructure\Repositories\Auth\UserVerificationRepository;
use App\Infrastructure\Repositories\Inventory\CategoryRepository;
use App\Infrastructure\Repositories\Inventory\CategoryVariationOptionRepository;
use App\Infrastructure\Repositories\Inventory\CategoryVariationRepository;
use App\Infrastructure\Repositories\Vendor\Onboarding\VendorBusinessInformationRepository;
use App\Infrastructure\Repositories\Vendor\Onboarding\VendorContactInformationRepository;
use App\Infrastructure\Repositories\Vendor\Onboarding\VendorLegalInformationRepository;
use App\Infrastructure\Repositories\Vendor\Onboarding\VendorPaymentInformationRepository;
use App\Infrastructure\Repositories\Vendor\Products\ProductImageRepository;
use App\Infrastructure\Repositories\Vendor\Products\ProductItemRepository;
use App\Infrastructure\Repositories\Vendor\Products\ProductRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        /* Authentication */
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(UserVerificationRepositoryInterface::class, UserVerificationRepository::class);

        /* Vendor Onboarding */
        $this->app->bind(VendorContactInformationRepositoryInterface::class,
            VendorContactInformationRepository::class);
        $this->app->bind(VendorBusinessInformationRepositoryInterface::class,
            VendorBusinessInformationRepository::class);
        $this->app->bind(VendorLegalInformationRepositoryInterface::class,
            VendorLegalInformationRepository::class);
        $this->app->bind(VendorPaymentInformationRepositoryInterface::class,
            VendorPaymentInformationRepository::class);

        /* Vendor Products */
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(ProductItemRepositoryInterface::class, ProductItemRepository::class);
        $this->app->bind(ProductImageRepositoryInterface::class, ProductImageRepository::class);

        /* Inventory */
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(CategoryVariationRepositoryInterface::class, CategoryVariationRepository::class);
        $this->app->bind(CategoryVariationOptionRepositoryInterface::class, CategoryVariationOptionRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
