<?php

namespace App\Domain\Admin\Actions\Category;

use App\Domain\Admin\Dtos\CreateCategoryVariationDto;
use App\Domain\Admin\Interfaces\Repositories\CategoryVariationRepositoryInterface;
use App\Domain\Admin\Resources\CategoryVariationResource;
use App\Domain\Inventory\Interfaces\Repositories\CategoryRepositoryInterface;

class CreateCategoryVariations
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly CategoryVariationRepositoryInterface $categoryVariationRepository,
    ) {}

    public function execute(CreateCategoryVariationDto $categoryVariationDto): CategoryVariationResource
    {
        $category = $this->categoryRepository->findByColumn('uuid', $categoryVariationDto->getCategoryUUID());

        $categoryVariationDto->setCategoryId($category->id);

        $variation = $this->categoryVariationRepository->store($categoryVariationDto);

        return new CategoryVariationResource($variation);
    }
}
