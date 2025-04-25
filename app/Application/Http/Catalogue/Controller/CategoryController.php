<?php

namespace App\Application\Http\Catalogue\Controller;

use App\Application\Shared\Responses\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController
{
    public function __construct() {}

    public function store(Request $request): JsonResponse
    {
        try {
            $data = $this->createSellerPaymentInformation->execute($sellerPaymentDto);

            return ApiResponse::success('Seller payment information successfully updated', $data);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }
}
