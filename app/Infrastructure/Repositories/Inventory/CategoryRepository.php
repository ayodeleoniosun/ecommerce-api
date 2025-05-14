<?php

namespace App\Infrastructure\Repositories\Inventory;

use App\Domain\Inventory\Interfaces\CategoryRepositoryInterface;
use App\Infrastructure\Models\Inventory\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    public function index(Request $request): LengthAwarePaginator
    {
        $search = $request->input('search') ?? null;

        return Category::with('subCategories')
            ->when($search, function ($query) use ($search) {
                $query->where('slug', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10);
    }
}
