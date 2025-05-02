<?php

namespace App\Domain\Admin\Interfaces\Repositories;

use App\Domain\Admin\Dtos\CreateCategoryVariationDto;
use App\Infrastructure\Models\CategoryVariation;

interface CategoryVariationRepositoryInterface
{
    public function store(CreateCategoryVariationDto $categoryVariationDto): CategoryVariation;
}
