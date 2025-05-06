<?php

namespace App\Infrastructure\Repositories\Inventory;

use App\Domain\Admin\Dtos\CreateCategoryVariationDto;
use App\Domain\Admin\Interfaces\CategoryVariationRepositoryInterface;
use App\Domain\Admin\Resources\Inventory\CategoryVariationResource;
use App\Infrastructure\Models\CategoryVariation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryVariationRepository extends BaseRepository implements CategoryVariationRepositoryInterface
{
    public function index(Request $request, string $categoryId): AnonymousResourceCollection
    {
        $search = $request->input('search') ?? null;

        $result = CategoryVariation::where('category_id', $categoryId)
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->latest()
            ->get();

        return CategoryVariationResource::collection($result);
    }

    public function store(CreateCategoryVariationDto $categoryVariationDto): void
    {
        CategoryVariation::insert($categoryVariationDto->toArray());
    }
}
