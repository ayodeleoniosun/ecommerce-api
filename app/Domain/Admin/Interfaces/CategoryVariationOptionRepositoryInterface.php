<?php

namespace App\Domain\Admin\Interfaces;

use App\Domain\Admin\Dtos\CreateCategoryVariationOptionDto;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

interface CategoryVariationOptionRepositoryInterface
{
    public function index(Request $request, string $variationId): AnonymousResourceCollection;

    public function store(CreateCategoryVariationOptionDto $categoryVariationOptionDto): void;
}
