<?php

namespace App\Domain\Vendor\Products\Actions;

use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Vendor\Products\Interfaces\ProductRepositoryInterface;
use App\Domain\Vendor\Products\Resource\ViewProductResource;
use App\Infrastructure\Models\Inventory\Product;

class ViewVendorProduct
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
    ) {}

    public function execute(string $productUUID): ViewProductResource
    {
        $product = $this->productRepository->findByColumn(Product::class, 'uuid', $productUUID);

        throw_if(! $product, ResourceNotFoundException::class, 'Product not found');

        return new ViewProductResource($this->productRepository->view($product));
    }
}
