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
        $sellerPaymentDto = new SellerPaymentInformationDto(
            $data->user_id,
            $data->account_name,
            $data->account_number,
            $data->bank_code,
            $data->bank_name,
            $data->swift_code
        );

        try {
            $data = $this->createSellerPaymentInformation->execute($sellerPaymentDto);

            return ApiResponse::success('Seller payment information successfully updated', $data);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }
}
