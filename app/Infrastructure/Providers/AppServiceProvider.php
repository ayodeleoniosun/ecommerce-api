<?php

namespace App\Infrastructure\Providers;

use App\Domain\Admin\Interfaces\CategoryVariationOptionRepositoryInterface;
use App\Domain\Admin\Interfaces\CategoryVariationRepositoryInterface;
use App\Domain\Auth\Interfaces\Repositories\UserRepositoryInterface;
use App\Domain\Auth\Interfaces\Repositories\UserVerificationRepositoryInterface;
use App\Domain\Inventory\Interfaces\CategoryRepositoryInterface;
use App\Domain\Vendor\Onboarding\Interfaces\SellerBusinessInformationRepositoryInterface;
use App\Domain\Vendor\Onboarding\Interfaces\SellerContactInformationRepositoryInterface;
use App\Domain\Vendor\Onboarding\Interfaces\SellerLegalInformationRepositoryInterface;
use App\Domain\Vendor\Onboarding\Interfaces\SellerPaymentInformationRepositoryInterface;
use App\Domain\Vendor\Products\Interfaces\ProductRepositoryInterface;
use App\Infrastructure\Repositories\Auth\UserRepository;
use App\Infrastructure\Repositories\Auth\UserVerificationRepository;
use App\Infrastructure\Repositories\Inventory\CategoryRepository;
use App\Infrastructure\Repositories\Inventory\CategoryVariationOptionRepository;
use App\Infrastructure\Repositories\Inventory\CategoryVariationRepository;
use App\Infrastructure\Repositories\Vendor\Onboarding\SellerBusinessInformationRepository;
use App\Infrastructure\Repositories\Vendor\Onboarding\SellerContactInformationRepository;
use App\Infrastructure\Repositories\Vendor\Onboarding\SellerLegalInformationRepository;
use App\Infrastructure\Repositories\Vendor\Onboarding\SellerPaymentInformationRepository;
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
        $this->app->bind(SellerContactInformationRepositoryInterface::class,
            SellerContactInformationRepository::class);
        $this->app->bind(SellerBusinessInformationRepositoryInterface::class,
            SellerBusinessInformationRepository::class);
        $this->app->bind(SellerLegalInformationRepositoryInterface::class,
            SellerLegalInformationRepository::class);
        $this->app->bind(SellerPaymentInformationRepositoryInterface::class,
            SellerPaymentInformationRepository::class);

        /* Vendor Products */
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);

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
