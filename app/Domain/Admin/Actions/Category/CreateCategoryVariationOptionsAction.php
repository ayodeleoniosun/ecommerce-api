<?php

namespace App\Domain\Admin\Actions\Category;

use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Admin\Dtos\Inventory\CreateCategoryVariationOptionDto;
use App\Domain\Admin\Interfaces\Inventory\CategoryVariationOptionRepositoryInterface;
use App\Domain\Admin\Interfaces\Inventory\CategoryVariationRepositoryInterface;
use App\Infrastructure\Models\Inventory\CategoryVariation;

class CreateCategoryVariationOptionsAction
{
    public function __construct(
        private readonly CategoryVariationRepositoryInterface $categoryVariationRepository,
        private readonly CategoryVariationOptionRepositoryInterface $categoryVariationOptionRepository,
    ) {}

    public function execute(CreateCategoryVariationOptionDto $categoryVariationOptionDto): array
    {
        $categoryVariation = $this->categoryVariationRepository->findByColumn(
            CategoryVariation::class,
            'uuid',
            $categoryVariationOptionDto->getCategoryVariationUUID()
        );

        throw_if(! $categoryVariation, ResourceNotFoundException::class, 'Category variation not found');

        $categoryVariationOptionDto->setCategoryVariationId($categoryVariation->id);

        $this->categoryVariationOptionRepository->store($categoryVariationOptionDto);

        return $categoryVariationOptionDto->getValues();
    }
}
