<?php

namespace App\Domain\Vendor\Products\Interfaces;

use App\Domain\Vendor\Products\Dtos\CreateProductDto;
use App\Infrastructure\Models\Product;

interface ProductRepositoryInterface
{
    public function store(CreateProductDto $createProductDto): Product;
}
