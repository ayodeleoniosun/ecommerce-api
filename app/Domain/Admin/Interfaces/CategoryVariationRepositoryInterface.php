<?php

namespace App\Domain\Admin\Interfaces;

use App\Domain\Admin\Dtos\CreateCategoryVariationDto;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

interface CategoryVariationRepositoryInterface
{
    public function index(Request $request, string $categoryId): AnonymousResourceCollection;

    public function store(CreateCategoryVariationDto $categoryVariationDto): void;
}
