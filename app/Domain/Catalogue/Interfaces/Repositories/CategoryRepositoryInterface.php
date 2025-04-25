<?php

namespace App\Domain\Catalogue\Interfaces\Repositories;

use App\Infrastructure\Models\Category;
use Illuminate\Http\Request;

interface CategoryRepositoryInterface
{
    public function index(Request $request): Category;
}
