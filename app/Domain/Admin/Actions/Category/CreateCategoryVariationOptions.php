<?php

namespace App\Domain\Admin\Actions\Category;

use App\Domain\Admin\Dtos\CreateCategoryVariationOptionDto;
use App\Domain\Admin\Interfaces\CategoryVariationOptionRepositoryInterface;
use App\Domain\Admin\Interfaces\CategoryVariationRepositoryInterface;
use App\Infrastructure\Models\Inventory\CategoryVariation;

class CreateCategoryVariationOptions
{
    public function __construct(
        private readonly CategoryVariationRepositoryInterface $categoryVariationRepository,
        private readonly CategoryVariationOptionRepositoryInterface $categoryVariationOptionRepository,
    ) {}

    public function execute(CreateCategoryVariationOptionDto $categoryVariationOptionDto): array
    {
        $categoryVariation = $this->categoryVariationRepository->findByColumn(CategoryVariation::class, 'uuid',
            $categoryVariationOptionDto->getCategoryVariationUUID());

        $categoryVariationOptionDto->setCategoryVariationId($categoryVariation->id);

        $this->categoryVariationOptionRepository->store($categoryVariationOptionDto);

        return $categoryVariationOptionDto->getValues();
    }
}
