<?php

namespace App\Domain\Vendor\Products\Controllers;

use App\Application\Shared\Responses\ApiResponse;
use App\Domain\Vendor\Products\Actions\CreateOrUpdateProduct;
use App\Domain\Vendor\Products\Actions\CreateOrUpdateProductItem;
use App\Domain\Vendor\Products\Dtos\CreateOrUpdateProductDto;
use App\Domain\Vendor\Products\Dtos\CreateOrUpdateProductItemDto;
use App\Domain\Vendor\Products\Requests\StoreOrUpdateProductItemRequest;
use App\Domain\Vendor\Products\Requests\StoreOrUpdateProductRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ProductController
{
    public function __construct(
        private readonly CreateOrUpdateProduct $createOrUpdateProduct,
        private readonly CreateOrUpdateProductItem $createOrUpdateProductItem,
    ) {}

    public function storeOrUpdateProduct(StoreOrUpdateProductRequest $request): JsonResponse
    {
        $product = CreateOrUpdateProductDto::fromRequest($request->validated());

        try {
            $data = $this->createOrUpdateProduct->execute($product);

            if ($data['is_existing_product'] || $product->getProductId()) {
                return ApiResponse::success('Product successfully updated', $data);
            }

            return ApiResponse::success('Product successfully added', $data, Response::HTTP_CREATED);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function storeOrUpdateProductItems(StoreOrUpdateProductItemRequest $request): JsonResponse
    {
        $productItem = CreateOrUpdateProductItemDto::fromRequest($request->validated());

        try {
            $data = $this->createOrUpdateProductItem->execute($productItem);

            if ($data['is_existing_product_item'] || $productItem->getProductItemId()) {
                return ApiResponse::success('Product item successfully updated', $data);
            }

            return ApiResponse::success('Product item successfully added', $data, Response::HTTP_CREATED);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }
}
