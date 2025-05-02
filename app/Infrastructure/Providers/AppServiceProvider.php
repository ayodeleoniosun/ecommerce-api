<?php

namespace App\Infrastructure\Providers;

use App\Domain\Admin\Interfaces\Repositories\CategoryVariationRepositoryInterface;
use App\Domain\Auth\Interfaces\Repositories\UserRepositoryInterface;
use App\Domain\Auth\Interfaces\Repositories\UserVerificationRepositoryInterface;
use App\Domain\Inventory\Interfaces\Repositories\CategoryRepositoryInterface;
use App\Domain\Onboarding\Interfaces\Repositories\SellerBusinessInformationRepositoryInterface;
use App\Domain\Onboarding\Interfaces\Repositories\SellerContactInformationRepositoryInterface;
use App\Domain\Onboarding\Interfaces\Repositories\SellerLegalInformationRepositoryInterface;
use App\Domain\Onboarding\Interfaces\Repositories\SellerPaymentInformationRepositoryInterface;
use App\Infrastructure\Repositories\Auth\UserRepository;
use App\Infrastructure\Repositories\Auth\UserVerificationRepository;
use App\Infrastructure\Repositories\Catalogue\CategoryRepository;
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
