<?php

namespace App\Domain\Vendor\Products\Interfaces;

use App\Domain\Vendor\Products\Dtos\CreateOrUpdateProductItemDto;
use App\Infrastructure\Models\Inventory\ProductItem;
use Illuminate\Database\Eloquent\Model;

interface ProductItemRepositoryInterface
{
    public function storeOrUpdate(CreateOrUpdateProductItemDto $createOrUpdateProductItemDto): ProductItem;

    public function findExistingProductItem(int $productId, string $variationOptionId): ?ProductItem;

    public function increaseStock(ProductItem $productItem, int $quantity): bool|int;

    public function decreaseStock(ProductItem|Model $productItem, int $quantity): bool|int;
}
