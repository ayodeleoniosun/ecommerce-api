<?php

namespace App\Domain\Vendor\Products\Actions;

use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Vendor\Products\Interfaces\ProductImageRepositoryInterface;
use App\Infrastructure\Models\Inventory\ProductImage;

class DeleteVendorProductImage
{
    public function __construct(
        private readonly ProductImageRepositoryInterface $productImageRepository,
    ) {}

    public function execute(string $productImageUUID): ?bool
    {
        $productImage = $this->productImageRepository->findByColumn(ProductImage::class, 'uuid', $productImageUUID);

        throw_if(! $productImage, ResourceNotFoundException::class, 'Product image not found');

        return $this->productImageRepository->delete($productImage);
    }
}
