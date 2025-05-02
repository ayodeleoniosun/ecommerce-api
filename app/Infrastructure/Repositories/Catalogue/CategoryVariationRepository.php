<?php

namespace App\Infrastructure\Repositories\Catalogue;

use App\Domain\Admin\Dtos\CreateCategoryVariationDto;
use App\Domain\Admin\Interfaces\Repositories\CategoryVariationRepositoryInterface;
use App\Infrastructure\Models\CategoryVariation;

class CategoryVariationRepository implements CategoryVariationRepositoryInterface
{
    public function store(CreateCategoryVariationDto $categoryVariationDto): CategoryVariation
    {
        return CategoryVariation::create($categoryVariationDto->toArray());
    }
}
