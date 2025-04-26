<?php

namespace App\Domain\Catalogue\Controllers;

use App\Application\Shared\Responses\ApiResponse;
use App\Domain\Catalogue\Actions\Category\GetProductCategories;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController
{
    public function __construct(private readonly GetProductCategories $getProductCategories) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $data = $this->getProductCategories->execute($request);

            return ApiResponse::success('Product categories successfully retrieved', $data);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }
}
