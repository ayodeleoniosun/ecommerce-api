<?php

namespace App\Domain\Inventory\Controllers;

use App\Application\Shared\Responses\ApiResponse;
use App\Domain\Inventory\Actions\Category\GetCategoryVariations;
use App\Domain\Inventory\Actions\Category\GetProductCategories;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController
{
    public function __construct(
        private readonly GetProductCategories $productCategories,
        private readonly GetCategoryVariations $categoryVariations,
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $data = $this->productCategories->execute($request);

            return ApiResponse::success('Product categories successfully retrieved', $data);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function variations(Request $request): JsonResponse
    {
        try {
            $data = $this->categoryVariations->execute($request);

            return ApiResponse::success('Category variations successfully retrieved', $data);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }
}
