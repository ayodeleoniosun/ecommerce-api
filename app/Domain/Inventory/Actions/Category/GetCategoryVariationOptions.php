<?php

namespace App\Domain\Inventory\Actions\Category;

use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Admin\Interfaces\CategoryVariationOptionRepositoryInterface;
use App\Domain\Admin\Interfaces\CategoryVariationRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class GetCategoryVariationOptions
{
    public function __construct(
        private readonly CategoryVariationOptionRepositoryInterface $categoryVariationOptionRepository,
        private readonly CategoryVariationRepositoryInterface $categoryVariationRepository,
    ) {}

    public function execute(Request $request, string $variationUUID): AnonymousResourceCollection
    {
        $categoryVariation = $this->categoryVariationRepository->findByColumn('uuid', $variationUUID);

        throw_if(! $categoryVariation, ResourceNotFoundException::class, 'Category variation not found');

        return $this->categoryVariationOptionRepository->index($request, $categoryVariation->id);
    }
}
