<?php

namespace App\Domain\Admin\Interfaces\Inventory;

use App\Domain\Admin\Dtos\Inventory\CreateCategoryVariationOptionDto;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

interface CategoryVariationOptionRepositoryInterface
{
    public function index(Request $request, string $variationId): Collection;

    public function store(CreateCategoryVariationOptionDto $categoryVariationOptionDto): void;
}
