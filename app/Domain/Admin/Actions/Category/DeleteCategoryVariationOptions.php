<?php

namespace App\Domain\Admin\Actions\Category;

use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Admin\Interfaces\Repositories\CategoryVariationOptionRepositoryInterface;

class DeleteCategoryVariationOptions
{
    public function __construct(
        private readonly CategoryVariationOptionRepositoryInterface $categoryVariationOptionRepository,
    ) {}

    public function execute(string $optionUUID): void
    {
        $categoryVariationOption = $this->categoryVariationOptionRepository->findByColumn('uuid', $optionUUID);

        throw_if(! $categoryVariationOption, ResourceNotFoundException::class, 'Category variation option not found');

        $this->categoryVariationOptionRepository->delete($categoryVariationOption);
    }
}
