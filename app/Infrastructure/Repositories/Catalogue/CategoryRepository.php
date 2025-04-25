<?php

namespace App\Infrastructure\Repositories\Catalogue;

use App\Domain\Catalogue\Interfaces\Repositories\CategoryRepositoryInterface;
use App\Infrastructure\Models\Category;
use Illuminate\Http\Request;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function index(Request $request): Category {}
}
