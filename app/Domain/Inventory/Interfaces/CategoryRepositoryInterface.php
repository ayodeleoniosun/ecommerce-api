<?php

namespace App\Domain\Inventory\Interfaces;

use App\Domain\Inventory\Resources\CategoryResourceCollection;
use App\Infrastructure\Models\Category;
use Illuminate\Http\Request;

interface CategoryRepositoryInterface
{
    public function index(Request $request): CategoryResourceCollection;

    public function findByColumn(string $field, string $value): ?Category;
}
