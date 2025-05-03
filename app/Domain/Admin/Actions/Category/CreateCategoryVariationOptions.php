<?php

namespace App\Domain\Admin\Actions\Category;

use App\Domain\Admin\Dtos\CreateCategoryVariationOptionDto;
use App\Domain\Admin\Interfaces\Repositories\CategoryVariationOptionRepositoryInterface;
use App\Domain\Admin\Interfaces\Repositories\CategoryVariationRepositoryInterface;

class CreateCategoryVariationOptions
{
    public function __construct(
        private readonly CategoryVariationRepositoryInterface $categoryVariationRepository,
        private readonly CategoryVariationOptionRepositoryInterface $categoryVariationOptionRepository,
    ) {}

    public function execute(CreateCategoryVariationOptionDto $categoryVariationOptionDto): array
    {
        $categoryVariation = $this->categoryVariationRepository->findByColumn('uuid',
            $categoryVariationOptionDto->getCategoryVariationUUID());

        $categoryVariationOptionDto->setCategoryVariationId($categoryVariation->id);

        $this->categoryVariationOptionRepository->store($categoryVariationOptionDto);

        return $categoryVariationOptionDto->getValues();
    }
}
