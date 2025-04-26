<?php

namespace App\Domain\Catalogue\Actions\Category;

use App\Domain\Catalogue\Interfaces\Repositories\CategoryRepositoryInterface;
use App\Domain\Catalogue\Resources\CategoryResourceCollection;
use Illuminate\Http\Request;

class GetProductCategories
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository,
    ) {}

    public function execute(Request $request): CategoryResourceCollection
    {
        return $this->categoryRepository->index($request);
    }
}
