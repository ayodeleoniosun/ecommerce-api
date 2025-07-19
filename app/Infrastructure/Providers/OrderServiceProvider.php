<?php

namespace App\Infrastructure\Providers;

use App\Domain\Order\Interfaces\Cart\UserCartItemRepositoryInterface;
use App\Domain\Order\Interfaces\Cart\UserCartRepositoryInterface;
use App\Domain\Order\Interfaces\Cart\WishlistRepositoryInterface;
use App\Domain\Order\Interfaces\Order\OrderItemRepositoryInterface;
use App\Domain\Order\Interfaces\Order\OrderPaymentRepositoryInterface;
use App\Domain\Order\Interfaces\Order\OrderRepositoryInterface;
use App\Domain\Order\Interfaces\Order\OrderShippingRepositoryInterface;
use App\Infrastructure\Repositories\Cart\UserCartItemRepository;
use App\Infrastructure\Repositories\Cart\UserCartRepository;
use App\Infrastructure\Repositories\Cart\WishlistRepository;
use App\Infrastructure\Repositories\Order\OrderItemRepository;
use App\Infrastructure\Repositories\Order\OrderPaymentRepository;
use App\Infrastructure\Repositories\Order\OrderRepository;
use App\Infrastructure\Repositories\Order\OrderShippingRepository;
use Illuminate\Support\ServiceProvider;

class OrderServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        /* Cart */
        $this->app->bind(UserCartRepositoryInterface::class, UserCartRepository::class);
        $this->app->bind(UserCartItemRepositoryInterface::class, UserCartItemRepository::class);

        /* Wishlist */
        $this->app->bind(WishlistRepositoryInterface::class, WishlistRepository::class);

        /* Order */
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->bind(OrderItemRepositoryInterface::class, OrderItemRepository::class);
        $this->app->bind(OrderShippingRepositoryInterface::class, OrderShippingRepository::class);
        $this->app->bind(OrderPaymentRepositoryInterface::class, OrderPaymentRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
