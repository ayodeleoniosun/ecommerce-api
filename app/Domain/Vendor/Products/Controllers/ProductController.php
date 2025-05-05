<?php

namespace App\Domain\Vendor\Products\Controllers;

use App\Application\Shared\Responses\ApiResponse;
use App\Domain\Vendor\Products\Actions\CreateProduct;
use App\Domain\Vendor\Products\Dtos\CreateProductDto;
use App\Domain\Vendor\Products\Requests\StoreProductRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ProductController
{
    public function __construct(
        private readonly CreateProduct $createProduct,
    ) {}

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = CreateProductDto::fromRequest($request->validated());

        try {
            $data = $this->createProduct->execute($product);

            return ApiResponse::success('Product successfully added', $data, Response::HTTP_CREATED);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }
}
