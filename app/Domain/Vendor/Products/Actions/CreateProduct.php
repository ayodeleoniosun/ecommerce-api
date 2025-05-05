<?php

namespace App\Domain\Vendor\Products\Actions;

use App\Domain\Vendor\Products\Dtos\CreateProductDto;
use App\Domain\Vendor\Products\Interfaces\ProductRepositoryInterface;
use App\Domain\Vendor\Products\Resource\ProductResource;

class CreateProduct
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
    ) {}

    public function execute(CreateProductDto $createProductDto): ProductResource
    {
        $product = $this->productRepository->store($createProductDto);

        return new ProductResource($product);
    }
}
