<?php

namespace App\Infrastructure\Repositories\Inventory;

use App\Domain\Admin\Dtos\CreateCategoryVariationOptionDto;
use App\Domain\Admin\Interfaces\CategoryVariationOptionRepositoryInterface;
use App\Domain\Admin\Resources\Inventory\CategoryVariationOptionResource;
use App\Infrastructure\Models\CategoryVariationOption;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryVariationOptionRepository extends BaseRepository implements CategoryVariationOptionRepositoryInterface
{
    public function index(Request $request, string $variationId): AnonymousResourceCollection
    {
        $search = $request->input('search') ?? null;

        $result = CategoryVariationOption::where('variation_id', $variationId)
            ->when($search, function ($query) use ($search) {
                $query->where('values', 'like', "%{$search}%");
            })
            ->latest()
            ->get();

        return CategoryVariationOptionResource::collection($result);
    }

    public function store(CreateCategoryVariationOptionDto $categoryVariationOptionDto): void
    {
        CategoryVariationOption::insert($categoryVariationOptionDto->toArray());
    }
}
