<?php

namespace App\Infrastructure\Providers;

use App\Domain\Shipping\Interfaces\PickupStation\PickupStationOpeningHourRepositoryInterface;
use App\Domain\Shipping\Interfaces\PickupStation\PickupStationRepositoryInterface;
use App\Infrastructure\Repositories\Shipping\PickupStation\PickupStationOpeningHourRepository;
use App\Infrastructure\Repositories\Shipping\PickupStation\PickupStationRepository;
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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
