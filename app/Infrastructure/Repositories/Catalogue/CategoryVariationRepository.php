<?php

namespace App\Infrastructure\Repositories\Catalogue;

use App\Domain\Admin\Dtos\CreateCategoryVariationDto;
use App\Domain\Admin\Interfaces\Repositories\CategoryVariationRepositoryInterface;
use App\Domain\Admin\Resources\CategoryVariationResource;
use App\Infrastructure\Models\CategoryVariation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryVariationRepository implements CategoryVariationRepositoryInterface
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $search = $request->input('search') ?? null;

        $result = CategoryVariation::query()
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10);

        return CategoryVariationResource::collection($result);
    }

    public function store(CreateCategoryVariationDto $categoryVariationDto): void
    {
        CategoryVariation::insert($categoryVariationDto->toArray());
    }

    public function findByColumn(string $field, string $value): ?CategoryVariation
    {
        return CategoryVariation::where($field, $value)->first();
    }
}
