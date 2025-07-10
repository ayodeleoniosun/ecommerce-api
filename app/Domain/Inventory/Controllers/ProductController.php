<?php

namespace App\Domain\Inventory\Controllers;

use App\Application\Shared\Responses\ApiResponse;
use App\Domain\Inventory\Actions\Products\GetAllProductsAction;
use App\Domain\Inventory\Actions\Products\ViewProductAction;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController
{
    public function __construct(
        private readonly GetAllProductsAction $getProducts,
        private readonly ViewProductAction $viewProduct,
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
