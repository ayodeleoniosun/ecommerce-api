<?php

namespace App\Infrastructure\Repositories\Inventory;

use App\Domain\Admin\Dtos\Inventory\CreateCategoryVariationOptionDto;
use App\Domain\Admin\Interfaces\Inventory\CategoryVariationOptionRepositoryInterface;
use App\Infrastructure\Models\Inventory\CategoryVariationOption;
use App\Infrastructure\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class CategoryVariationOptionRepository extends BaseRepository implements CategoryVariationOptionRepositoryInterface
{
    public function index(Request $request, string $variationId): Collection
    {
        $search = $request->input('search') ?? null;

        return CategoryVariationOption::where('variation_id', $variationId)
            ->when($search, function ($query) use ($search) {
                $query->where('values', 'like', "%{$search}%");
            })
            ->latest()
            ->get();
    }

    public function store(CreateCategoryVariationOptionDto $categoryVariationOptionDto): void
    {
        CategoryVariationOption::insert($categoryVariationOptionDto->toArray());
    }
}
