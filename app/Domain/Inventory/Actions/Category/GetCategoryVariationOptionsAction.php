<?php

namespace App\Domain\Inventory\Actions\Category;

use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Admin\Interfaces\Inventory\CategoryVariationOptionRepositoryInterface;
use App\Domain\Admin\Interfaces\Inventory\CategoryVariationRepositoryInterface;
use App\Domain\Admin\Resources\Inventory\CategoryVariationOptionResource;
use App\Infrastructure\Models\Inventory\CategoryVariation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class GetCategoryVariationOptionsAction
{
    public function __construct(
        private readonly CategoryVariationOptionRepositoryInterface $categoryVariationOptionRepository,
        private readonly CategoryVariationRepositoryInterface $categoryVariationRepository,
    ) {}

    public function execute(Request $request, string $variationUUID): AnonymousResourceCollection
    {
        $categoryVariation = $this->categoryVariationRepository->findByColumn(CategoryVariation::class, 'uuid',
            $variationUUID);

        throw_if(! $categoryVariation, ResourceNotFoundException::class, 'Category variation not found');

        $options = $this->categoryVariationOptionRepository->index($request, $categoryVariation->id);

        return CategoryVariationOptionResource::collection($options);
    }
}
