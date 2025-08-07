<?php

namespace App\Domain\Vendor\Products\Interfaces;

use App\Domain\Vendor\Products\Dtos\CreateOrUpdateProductItemDto;
use App\Infrastructure\Models\Inventory\ProductItem;
use Illuminate\Database\Eloquent\Model;

interface ProductItemRepositoryInterface
{
    public function storeOrUpdate(CreateOrUpdateProductItemDto $createOrUpdateProductItemDto): ProductItem;

    public function findExistingProductItem(int $productId, string $variationOptionId): ?ProductItem;

    public function increaseStock(Model $productItem, int $quantity): bool|int;

    public function decreaseStock(Model $productItem, int $quantity): Model;
}
