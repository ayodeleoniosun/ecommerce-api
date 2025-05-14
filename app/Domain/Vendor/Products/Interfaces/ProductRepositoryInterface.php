<?php

namespace App\Domain\Vendor\Products\Interfaces;

use App\Domain\Vendor\Products\Dtos\CreateOrUpdateProductDto;
use App\Infrastructure\Models\Inventory\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

interface ProductRepositoryInterface
{
    public function index(Request $request): LengthAwarePaginator;

    public function view(Product $product): Product;

    public function storeOrUpdate(CreateOrUpdateProductDto $createOrUpdateProductDto): Product;

    public function findExistingProduct(int $vendorId, string $name): ?Product;
}
