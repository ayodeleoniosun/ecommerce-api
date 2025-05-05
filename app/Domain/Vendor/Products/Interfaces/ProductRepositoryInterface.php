<?php

namespace App\Domain\Vendor\Products\Interfaces;

use App\Domain\Vendor\Products\Dtos\CreateProductDto;
use App\Infrastructure\Models\Product;

interface ProductRepositoryInterface
{
    public function store(CreateProductDto $createProductDto): Product;

    //    public function index(Request $request, string $categoryId): AnonymousResourceCollection;
    //
    //    public function findByColumn(string $field, string $value): ?CategoryVariation;
    //
    //    public function delete(CategoryVariation $variation): void;

}
