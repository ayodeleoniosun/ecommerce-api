<?php

namespace App\Domain\Order\Controllers;

class CartController
{
    //    public function addToCart(AddToCartRequest $request): JsonResponse
    //    {
    //        $product = CreateOrUpdateProductDto::fromRequest($request->validated());
    //
    //        try {
    //            $data = $this->createOrUpdateProduct->execute($product);
    //
    //            if ($data['is_existing_product'] || $product->getProductId()) {
    //                return ApiResponse::success('Product successfully updated', $data);
    //            }
    //
    //            return ApiResponse::success('Product successfully added', $data, Response::HTTP_CREATED);
    //        } catch (Exception $e) {
    //            return ApiResponse::error($e->getMessage(), $e->getCode());
    //        }
    //    }
}
