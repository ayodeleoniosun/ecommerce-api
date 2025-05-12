<?php

namespace App\Domain\Vendor\Products\Interfaces;

use App\Domain\Vendor\Products\Dtos\CreateOrUpdateProductItemDto;
use App\Infrastructure\Models\ProductItem;

interface ProductItemRepositoryInterface
{
    public function storeOrUpdate(CreateOrUpdateProductItemDto $createOrUpdateProductItemDto): ProductItem;

    public function lockItem(int $productItemId): ?ProductItem;
}
