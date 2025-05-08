<?php

namespace App\Domain\Inventory\Actions\Category;

use App\Domain\Inventory\Interfaces\CategoryRepositoryInterface;
use App\Domain\Inventory\Resources\Category\CategoryResourceCollection;
use Illuminate\Http\Request;

class GetProductCategories
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository,
    ) {}

    public function execute(Request $request): CategoryResourceCollection
    {
        $categories = $this->categoryRepository->index($request);

        return new CategoryResourceCollection($categories);
    }
}
