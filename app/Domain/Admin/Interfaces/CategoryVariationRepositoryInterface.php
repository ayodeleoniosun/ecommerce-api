<?php

namespace App\Domain\Admin\Interfaces;

use App\Domain\Admin\Dtos\CreateCategoryVariationDto;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

interface CategoryVariationRepositoryInterface
{
    public function index(Request $request, string $categoryId): Collection;

    public function store(CreateCategoryVariationDto $categoryVariationDto): void;
}
