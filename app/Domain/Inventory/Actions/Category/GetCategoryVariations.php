<?php

namespace App\Domain\Inventory\Actions\Category;

use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Admin\Interfaces\CategoryVariationRepositoryInterface;
use App\Domain\Admin\Resources\Inventory\CategoryVariationResource;
use App\Domain\Inventory\Interfaces\CategoryRepositoryInterface;
use App\Infrastructure\Models\Category;
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
        $category = $this->categoryRepository->findByColumn(Category::class, 'uuid', $categoryUUID);

        throw_if(! $category, ResourceNotFoundException::class, 'Category not found');

        $variations = $this->categoryVariationRepository->index($request, $category->id);

        return CategoryVariationResource::collection($variations);
    }
}
