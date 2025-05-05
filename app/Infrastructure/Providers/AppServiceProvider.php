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
use App\Infrastructure\Repositories\Auth\UserRepository;
use App\Infrastructure\Repositories\Auth\UserVerificationRepository;
use App\Infrastructure\Repositories\Catalogue\CategoryRepository;
use App\Infrastructure\Repositories\Catalogue\CategoryVariationOptionRepository;
use App\Infrastructure\Repositories\Catalogue\CategoryVariationRepository;
use App\Infrastructure\Repositories\Onboarding\SellerBusinessInformationRepository;
use App\Infrastructure\Repositories\Onboarding\SellerContactInformationRepository;
use App\Infrastructure\Repositories\Onboarding\SellerLegalInformationRepository;
use App\Infrastructure\Repositories\Onboarding\SellerPaymentInformationRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);

        $this->app->bind(UserVerificationRepositoryInterface::class, UserVerificationRepository::class);

        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);

        $this->app->bind(CategoryVariationRepositoryInterface::class, CategoryVariationRepository::class);

        $this->app->bind(CategoryVariationOptionRepositoryInterface::class, CategoryVariationOptionRepository::class);

        $this->app->bind(SellerContactInformationRepositoryInterface::class,
            SellerContactInformationRepository::class);

        $this->app->bind(SellerBusinessInformationRepositoryInterface::class,
            SellerBusinessInformationRepository::class);

        $this->app->bind(SellerLegalInformationRepositoryInterface::class,
            SellerLegalInformationRepository::class);

        $this->app->bind(SellerPaymentInformationRepositoryInterface::class,
            SellerPaymentInformationRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
