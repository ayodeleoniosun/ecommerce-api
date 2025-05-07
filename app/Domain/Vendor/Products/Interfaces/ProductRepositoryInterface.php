<?php

namespace App\Domain\Vendor\Products\Interfaces;

use App\Domain\Vendor\Products\Dtos\CreateOrUpdateProductDto;
use App\Domain\Vendor\Products\Resource\ProductResourceCollection;
use App\Infrastructure\Models\Product;
use Illuminate\Http\Request;

interface ProductRepositoryInterface
{
    public function index(Request $request): ProductResourceCollection;

    public function storeOrUpdate(CreateOrUpdateProductDto $createOrUpdateProductDto): Product;

    public function findExistingProduct(int $vendorId, string $name): ?Product;
}
