<?php

namespace App\Domain\Admin\Actions\Category;

use App\Domain\Admin\Dtos\Inventory\CreateCategoryVariationDto;
use App\Domain\Admin\Interfaces\Inventory\CategoryVariationRepositoryInterface;
use App\Domain\Inventory\Interfaces\CategoryRepositoryInterface;
use App\Infrastructure\Models\Inventory\Category;

class CreateCategoryVariationsAction
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly CategoryVariationRepositoryInterface $categoryVariationRepository,
    ) {}

    public function execute(CreateCategoryVariationDto $categoryVariationDto): array
    {
        $category = $this->categoryRepository->findByColumn(Category::class, 'uuid',
            $categoryVariationDto->getCategoryUUID());

        $categoryVariationDto->setCategoryId($category->id);

        $this->categoryVariationRepository->store($categoryVariationDto);

        return $categoryVariationDto->getVariations();
    }
}
