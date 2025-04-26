<?php

namespace App\Infrastructure\Repositories\Catalogue;

use App\Domain\Catalogue\Interfaces\Repositories\CategoryRepositoryInterface;
use App\Domain\Catalogue\Resources\CategoryResourceCollection;
use App\Infrastructure\Models\Category;
use Illuminate\Http\Request;

class CategoryRepository implements CategoryRepositoryInterface
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
