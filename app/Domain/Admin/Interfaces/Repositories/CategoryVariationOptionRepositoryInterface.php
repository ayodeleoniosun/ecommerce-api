<?php

namespace App\Domain\Admin\Interfaces\Repositories;

use App\Domain\Admin\Dtos\CreateCategoryVariationOptionDto;
use App\Infrastructure\Models\CategoryVariationOption;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

interface CategoryVariationOptionRepositoryInterface
{
    public function index(Request $request, string $variationId): AnonymousResourceCollection;

    public function store(CreateCategoryVariationOptionDto $categoryVariationOptionDto): void;

    public function findByColumn(string $field, string $value): ?CategoryVariationOption;

    public function delete(CategoryVariationOption $option): void;
}
