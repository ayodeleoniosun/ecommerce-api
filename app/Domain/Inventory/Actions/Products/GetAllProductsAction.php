<?php

namespace App\Domain\Inventory\Actions\Products;

use App\Domain\Vendor\Products\Interfaces\ProductRepositoryInterface;
use App\Domain\Vendor\Products\Resource\ProductResourceCollection;
use Illuminate\Http\Request;

class GetAllProductsAction
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
    ) {}

    public function execute(Request $request): ProductResourceCollection
    {
        $products = $this->productRepository->index($request);

        return new ProductResourceCollection($products);
    }
}
