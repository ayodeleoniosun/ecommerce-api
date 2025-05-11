<?php

namespace App\Domain\Vendor\Products\Actions;

use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Vendor\Products\Interfaces\ProductRepositoryInterface;
use App\Infrastructure\Models\Product;

class DeleteVendorProduct
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
    ) {}

    public function execute(string $productUUID): ?bool
    {
        $product = $this->productRepository->findByColumn(Product::class, 'uuid', $productUUID);

        throw_if(! $product, ResourceNotFoundException::class, 'Product not found');

        return $this->productRepository->delete($product);
    }
}
