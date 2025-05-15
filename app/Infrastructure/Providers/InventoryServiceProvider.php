<?php

namespace App\Infrastructure\Providers;

use App\Domain\Admin\Interfaces\Inventory\CategoryVariationOptionRepositoryInterface;
use App\Domain\Admin\Interfaces\Inventory\CategoryVariationRepositoryInterface;
use App\Domain\Inventory\Interfaces\CategoryRepositoryInterface;
use App\Domain\Vendor\Products\Interfaces\ProductImageRepositoryInterface;
use App\Domain\Vendor\Products\Interfaces\ProductItemRepositoryInterface;
use App\Domain\Vendor\Products\Interfaces\ProductRepositoryInterface;
use App\Infrastructure\Repositories\Inventory\CategoryRepository;
use App\Infrastructure\Repositories\Inventory\CategoryVariationOptionRepository;
use App\Infrastructure\Repositories\Inventory\CategoryVariationRepository;
use App\Infrastructure\Repositories\Vendor\Products\ProductImageRepository;
use App\Infrastructure\Repositories\Vendor\Products\ProductItemRepository;
use App\Infrastructure\Repositories\Vendor\Products\ProductRepository;
use Illuminate\Support\ServiceProvider;

class InventoryServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        /* Product */
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
