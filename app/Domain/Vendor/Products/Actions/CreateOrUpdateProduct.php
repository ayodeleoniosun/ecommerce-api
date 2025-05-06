<?php

namespace App\Domain\Vendor\Products\Actions;

use App\Domain\Vendor\Products\Dtos\CreateOrUpdateProductDto;
use App\Domain\Vendor\Products\Interfaces\ProductRepositoryInterface;
use App\Domain\Vendor\Products\Resource\ProductResource;

class CreateOrUpdateProduct
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
    ) {}

    public function execute(CreateOrUpdateProductDto $createOrUpdateProductDto): array
    {
        $existingProduct = $this->productRepository->findExistingProduct(
            $createOrUpdateProductDto->getVendorId(),
            $createOrUpdateProductDto->getName(),
        );

        $product = $this->productRepository->storeOrUpdate($createOrUpdateProductDto);

        return [
            'is_existing_product' => (bool) $existingProduct,
            'product' => new ProductResource($product),
        ];
    }
}
