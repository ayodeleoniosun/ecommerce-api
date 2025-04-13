<?php

namespace App\Providers;

use App\Domain\Auth\Interfaces\Repositories\UserRepositoryInterface;
use App\Domain\Auth\Interfaces\Repositories\UserVerificationRepositoryInterface;
use App\Domain\Onboarding\Interfaces\Repositories\SellerBusinessInformationRepositoryInterface;
use App\Domain\Onboarding\Interfaces\Repositories\SellerContactInformationRepositoryInterface;
use App\Infrastructure\Repositories\Auth\UserRepository;
use App\Infrastructure\Repositories\Auth\UserVerificationRepository;
use App\Infrastructure\Repositories\Onboarding\SellerBusinessInformationRepository;
use App\Infrastructure\Repositories\Onboarding\SellerContactInformationRepository;
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

        $this->app->bind(SellerContactInformationRepositoryInterface::class,
            SellerContactInformationRepository::class);

        $this->app->bind(SellerBusinessInformationRepositoryInterface::class,
            SellerBusinessInformationRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
