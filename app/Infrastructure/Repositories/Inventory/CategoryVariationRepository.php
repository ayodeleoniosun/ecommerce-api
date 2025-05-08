<?php

namespace App\Infrastructure\Repositories\Inventory;

use App\Domain\Admin\Dtos\CreateCategoryVariationDto;
use App\Domain\Admin\Interfaces\CategoryVariationRepositoryInterface;
use App\Infrastructure\Models\CategoryVariation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class CategoryVariationRepository extends BaseRepository implements CategoryVariationRepositoryInterface
{
    public function index(Request $request, string $categoryId): Collection
    {
        $search = $request->input('search') ?? null;

        return CategoryVariation::where('category_id', $categoryId)
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->latest()
            ->get();
    }

    public function store(CreateCategoryVariationDto $categoryVariationDto): void
    {
        CategoryVariation::insert($categoryVariationDto->toArray());
    }
}
