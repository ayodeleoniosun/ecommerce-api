<?php

namespace App\Domain\Inventory\Actions\Category;

use App\Infrastructure\Repositories\Catalogue\CategoryVariationRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class GetCategoryVariations
{
    public function __construct(
        private readonly CategoryVariationRepository $categoryVariationRepository,
    ) {}

    public function execute(Request $request): AnonymousResourceCollection
    {
        return $this->categoryVariationRepository->index($request);
    }
}
