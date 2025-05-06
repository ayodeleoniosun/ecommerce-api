<?php

namespace App\Domain\Inventory\Interfaces;

use App\Domain\Inventory\Resources\CategoryResourceCollection;
use Illuminate\Http\Request;

interface CategoryRepositoryInterface
{
    public function index(Request $request): CategoryResourceCollection;
}
