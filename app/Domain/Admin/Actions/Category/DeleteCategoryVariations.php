<?php

namespace App\Domain\Admin\Actions\Category;

use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Admin\Interfaces\CategoryVariationRepositoryInterface;
use App\Infrastructure\Models\CategoryVariation;

class DeleteCategoryVariations
{
    public function __construct(
        private readonly CategoryVariationRepositoryInterface $categoryVariationRepository,
    ) {}

    public function execute(string $variationUUID): void
    {
        $categoryVariation = $this->categoryVariationRepository->findByColumn(CategoryVariation::class, 'uuid',
            $variationUUID);

        throw_if(! $categoryVariation, ResourceNotFoundException::class, 'Category variation not found');

        $this->categoryVariationRepository->delete($categoryVariation);
    }
}
