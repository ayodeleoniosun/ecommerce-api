<?php

namespace App\Domain\Inventory\Controllers;

use App\Application\Shared\Responses\ApiResponse;
use App\Domain\Inventory\Actions\Category\GetCategoryVariationOptionsAction;
use App\Domain\Inventory\Actions\Category\GetCategoryVariationsAction;
use App\Domain\Inventory\Actions\Category\GetProductCategoriesAction;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController
{
    public function __construct(
        private readonly GetProductCategoriesAction $productCategories,
        private readonly GetCategoryVariationsAction $categoryVariations,
        private readonly GetCategoryVariationOptionsAction $variationOptions,
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

    public function getCategoryVariations(Request $request, string $categoryUUID): JsonResponse
    {
        try {
            $data = $this->categoryVariations->execute($request, $categoryUUID);

            return ApiResponse::success('Category variations successfully retrieved', $data);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function getCategoryVariationOptions(Request $request, string $variationUUID): JsonResponse
    {
        try {
            $data = $this->variationOptions->execute($request, $variationUUID);

            return ApiResponse::success('Category variations successfully retrieved', $data);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }
}
