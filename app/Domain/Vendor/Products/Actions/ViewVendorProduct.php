<?php

namespace App\Domain\Vendor\Products\Actions;

use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Vendor\Products\Interfaces\ProductRepositoryInterface;
use App\Domain\Vendor\Products\Resource\ViewProductResource;
use App\Infrastructure\Models\Product;

class ViewVendorProduct
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
    ) {}

    public function execute(string $productItemUUID)
    {
        $product = $this->productRepository->findByColumn(Product::class, 'uuid', $productItemUUID);

        throw_if(! $product, ResourceNotFoundException::class, 'Product not found');

        return new ViewProductResource($this->productRepository->view($product));
    }
}
