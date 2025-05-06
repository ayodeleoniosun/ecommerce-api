<?php

namespace App\Infrastructure\Repositories\Inventory;

use App\Domain\Inventory\Interfaces\CategoryRepositoryInterface;
use App\Domain\Inventory\Resources\CategoryResourceCollection;
use App\Infrastructure\Models\Category;
use Illuminate\Http\Request;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    public function index(Request $request): CategoryResourceCollection
    {
        $search = $request->input('search') ?? null;

        $result = Category::with('subCategories')
            ->when($search, function ($query) use ($search) {
                $query->where('slug', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10);

        return new CategoryResourceCollection($result);
    }
}
