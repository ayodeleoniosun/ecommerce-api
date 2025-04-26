<?php

namespace App\Domain\Catalogue\Interfaces\Repositories;

use App\Domain\Catalogue\Resources\CategoryResourceCollection;
use Illuminate\Http\Request;

interface CategoryRepositoryInterface
{
    public function index(Request $request): CategoryResourceCollection;
}
