<?php

namespace App\Domain\Inventory\Controllers;

use App\Application\Shared\Responses\ApiResponse;
use App\Domain\Inventory\Actions\Products\GetAllProducts;
use App\Domain\Inventory\Actions\Products\ViewProduct;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController
{
    public function __construct(
        private readonly GetAllProducts $getProducts,
        private readonly ViewProduct $viewProduct,
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $data = $this->getProducts->execute($request);

            return ApiResponse::success('Products successfully retrieved', $data);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function view(string $productUUID): JsonResponse
    {
        try {
            $data = $this->viewProduct->execute($productUUID);

            return ApiResponse::success('Product successfully retrieved', $data);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }
}
