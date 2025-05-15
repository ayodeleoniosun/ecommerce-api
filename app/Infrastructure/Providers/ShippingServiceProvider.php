<?php

namespace App\Infrastructure\Providers;

use App\Domain\Shipping\Interfaces\PickupStation\PickupStationOpeningHourRepositoryInterface;
use App\Domain\Shipping\Interfaces\PickupStation\PickupStationRepositoryInterface;
use App\Domain\Shipping\Interfaces\ShippingAddress\CustomerShippingAddressRepositoryInterface;
use App\Infrastructure\Repositories\Shipping\PickupStation\PickupStationOpeningHourRepository;
use App\Infrastructure\Repositories\Shipping\PickupStation\PickupStationRepository;
use App\Infrastructure\Repositories\Shipping\ShippingAddress\CustomerShippingAddressRepository;
use Illuminate\Support\ServiceProvider;

class ShippingServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        /* Pickup Station */
        $this->app->bind(PickupStationRepositoryInterface::class, PickupStationRepository::class);
        $this->app->bind(PickupStationOpeningHourRepositoryInterface::class, PickupStationOpeningHourRepository::class);

        /* Shipping Address */
        $this->app->bind(CustomerShippingAddressRepositoryInterface::class, CustomerShippingAddressRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
