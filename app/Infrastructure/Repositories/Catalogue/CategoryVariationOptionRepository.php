<?php

namespace App\Infrastructure\Repositories\Catalogue;

use App\Domain\Admin\Dtos\CreateCategoryVariationOptionDto;
use App\Domain\Admin\Interfaces\CategoryVariationOptionRepositoryInterface;
use App\Domain\Admin\Resources\CategoryVariationOptionResource;
use App\Infrastructure\Models\CategoryVariationOption;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryVariationOptionRepository implements CategoryVariationOptionRepositoryInterface
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

    public function findByColumn(string $field, string $value): ?CategoryVariationOption
    {
        return CategoryVariationOption::where($field, $value)->first();
    }

    public function delete(CategoryVariationOption $option): void
    {
        $option->delete();
    }
}
