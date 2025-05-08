<?php

namespace App\Domain\Admin\Interfaces;

use App\Domain\Admin\Dtos\CreateCategoryVariationOptionDto;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

interface CategoryVariationOptionRepositoryInterface
{
    public function index(Request $request, string $variationId): Collection;

    public function store(CreateCategoryVariationOptionDto $categoryVariationOptionDto): void;
}
