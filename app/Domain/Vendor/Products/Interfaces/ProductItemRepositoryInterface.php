<?php

namespace App\Domain\Vendor\Products\Interfaces;

use App\Domain\Vendor\Products\Dtos\CreateProductItemDto;
use App\Infrastructure\Models\ProductItem;

interface ProductItemRepositoryInterface
{
    public function store(CreateProductItemDto $createProductItemDto): ProductItem;
}
