<?php

namespace App\Domain\Vendor\Products\Actions;

use App\Domain\Vendor\Products\Interfaces\ProductRepositoryInterface;
use App\Domain\Vendor\Products\Resource\ProductResourceCollection;
use Illuminate\Http\Request;

class GetVendorProducts
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
    ) {}

    public function execute(Request $request): ProductResourceCollection
    {
        return $this->productRepository->index($request);
    }
}
