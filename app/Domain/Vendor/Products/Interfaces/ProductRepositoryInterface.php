<?php

namespace App\Domain\Vendor\Products\Interfaces;

use App\Domain\Vendor\Products\Dtos\CreateOrUpdateProductDto;
use App\Infrastructure\Models\Product;

interface ProductRepositoryInterface
{
    public function storeOrUpdate(CreateOrUpdateProductDto $createOrUpdateProductDto): Product;

    public function findExistingProduct(int $vendorId, string $name): ?Product;
}
