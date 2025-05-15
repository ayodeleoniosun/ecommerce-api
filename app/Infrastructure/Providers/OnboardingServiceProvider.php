<?php

namespace App\Infrastructure\Providers;

use App\Domain\Vendor\Onboarding\Interfaces\VendorBusinessInformationRepositoryInterface;
use App\Domain\Vendor\Onboarding\Interfaces\VendorContactInformationRepositoryInterface;
use App\Domain\Vendor\Onboarding\Interfaces\VendorLegalInformationRepositoryInterface;
use App\Domain\Vendor\Onboarding\Interfaces\VendorPaymentInformationRepositoryInterface;
use App\Infrastructure\Repositories\Vendor\Onboarding\VendorBusinessInformationRepository;
use App\Infrastructure\Repositories\Vendor\Onboarding\VendorContactInformationRepository;
use App\Infrastructure\Repositories\Vendor\Onboarding\VendorLegalInformationRepository;
use App\Infrastructure\Repositories\Vendor\Onboarding\VendorPaymentInformationRepository;
use Illuminate\Support\ServiceProvider;

class OnboardingServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(VendorContactInformationRepositoryInterface::class,
            VendorContactInformationRepository::class);
        $this->app->bind(VendorBusinessInformationRepositoryInterface::class,
            VendorBusinessInformationRepository::class);
        $this->app->bind(VendorLegalInformationRepositoryInterface::class,
            VendorLegalInformationRepository::class);
        $this->app->bind(VendorPaymentInformationRepositoryInterface::class,
            VendorPaymentInformationRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
