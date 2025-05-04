<?php

namespace App\Domain\Inventory\Actions\Category;

use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Admin\Interfaces\Repositories\CategoryVariationRepositoryInterface;
use App\Domain\Inventory\Interfaces\Repositories\CategoryRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class GetCategoryVariations
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly CategoryVariationRepositoryInterface $categoryVariationRepository,
    ) {}

    public function execute(Request $request, string $categoryUUID): AnonymousResourceCollection
    {
        $category = $this->categoryRepository->findByColumn('uuid', $categoryUUID);

        throw_if(! $category, ResourceNotFoundException::class, 'Category not found');

        return $this->categoryVariationRepository->index($request, $category->id);
    }
}
