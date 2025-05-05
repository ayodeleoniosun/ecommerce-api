<?php

namespace App\Domain\Vendor\Products\Controllers;

use App\Application\Shared\Responses\ApiResponse;
use App\Domain\Vendor\Products\Requests\StoreProductRequest;
use Exception;
use Illuminate\Http\JsonResponse;

class ProductController
{
    public function __construct() {}

    public function store(StoreProductRequest $request): JsonResponse
    {
        try {
            $data = $this->productCategories->execute($request);

            return ApiResponse::success('Product successfully added', $data);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }
}
