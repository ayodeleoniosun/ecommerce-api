<?php

namespace App\Infrastructure\Providers;

use App\Domain\Order\Interfaces\GuestCartItemRepositoryInterface;
use App\Domain\Order\Interfaces\GuestCartRepositoryInterface;
use App\Domain\Order\Interfaces\UserCartItemRepositoryInterface;
use App\Domain\Order\Interfaces\UserCartRepositoryInterface;
use App\Infrastructure\Repositories\Order\GuestCartItemRepository;
use App\Infrastructure\Repositories\Order\GuestCartRepository;
use App\Infrastructure\Repositories\Order\UserCartItemRepository;
use App\Infrastructure\Repositories\Order\UserCartRepository;
use Illuminate\Support\ServiceProvider;

class OrderServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        /* Cart */
        $this->app->bind(GuestCartRepositoryInterface::class, GuestCartRepository::class);
        $this->app->bind(GuestCartItemRepositoryInterface::class, GuestCartItemRepository::class);
        $this->app->bind(UserCartRepositoryInterface::class, UserCartRepository::class);
        $this->app->bind(UserCartItemRepositoryInterface::class, UserCartItemRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
