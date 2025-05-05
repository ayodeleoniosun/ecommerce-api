<?php

namespace App\Infrastructure\Repositories\Vendor\Products;

use App\Domain\Vendor\Products\Dtos\CreateProductDto;
use App\Domain\Vendor\Products\Interfaces\ProductRepositoryInterface;
use App\Infrastructure\Models\Product;

class ProductRepository implements ProductRepositoryInterface
{
    public function store(CreateProductDto $createProductDto): Product
    {
        $product = Product::updateOrCreate(
            ['vendor_id' => $createProductDto->getVendorId(), 'name' => $createProductDto->getName()],
            $createProductDto->toArray(),
        );

        $product->load('vendor', 'category');

        return $product;
    }
}
