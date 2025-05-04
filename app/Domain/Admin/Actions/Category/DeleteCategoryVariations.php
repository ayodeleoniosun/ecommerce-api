<?php

namespace App\Domain\Admin\Actions\Category;

use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Admin\Interfaces\Repositories\CategoryVariationRepositoryInterface;

class DeleteCategoryVariations
{
    public function __construct(
        private readonly CategoryVariationRepositoryInterface $categoryVariationRepository,
    ) {}

    public function execute(string $variationUUID): void
    {
        $categoryVariation = $this->categoryVariationRepository->findByColumn('uuid', $variationUUID);

        throw_if(! $categoryVariation, ResourceNotFoundException::class, 'Category variation not found');

        $this->categoryVariationRepository->delete($categoryVariation);
    }
}
