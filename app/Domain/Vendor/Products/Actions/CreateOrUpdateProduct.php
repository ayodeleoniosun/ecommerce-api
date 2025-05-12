<?php

namespace App\Domain\Vendor\Products\Actions;

use App\Domain\Vendor\Products\Dtos\CreateOrUpdateProductDto;
use App\Domain\Vendor\Products\Interfaces\ProductRepositoryInterface;
use App\Domain\Vendor\Products\Resource\AllProductResource;

class CreateOrUpdateProduct
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
    ) {}

    public function execute(CreateOrUpdateProductDto $createOrUpdateProductDto): array
    {
        $isExist = (bool) $this->productRepository->findExistingProduct($createOrUpdateProductDto->getVendorId(),
            $createOrUpdateProductDto->getName());
        $isUpdating = (bool) $createOrUpdateProductDto->getProductId();

        $product = $this->productRepository->storeOrUpdate($createOrUpdateProductDto);

        return [
            'is_existing_product' => $isExist || $isUpdating,
            'product' => new AllProductResource($product),
        ];
    }
}
