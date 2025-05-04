<?php

namespace App\Domain\Admin\Interfaces\Repositories;

use App\Domain\Admin\Dtos\CreateCategoryVariationDto;
use App\Infrastructure\Models\CategoryVariation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

interface CategoryVariationRepositoryInterface
{
    public function index(Request $request, string $categoryId): AnonymousResourceCollection;

    public function store(CreateCategoryVariationDto $categoryVariationDto): void;

    public function findByColumn(string $field, string $value): ?CategoryVariation;
}
