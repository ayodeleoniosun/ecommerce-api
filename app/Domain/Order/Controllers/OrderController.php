<?php

namespace App\Domain\Order\Controllers;

use App\Application\Shared\Responses\ApiResponse;
use App\Domain\Order\Actions\Cart\Checkout;
use App\Domain\Order\Dtos\CheckoutDto;
use App\Domain\Order\Requests\CheckoutRequest;
use Exception;
use Illuminate\Http\JsonResponse;

class OrderController
{
    public function __construct(
        private readonly Checkout $checkout,
    ) {}

    public function store(CheckoutRequest $request): JsonResponse
    {
        $checkoutDto = CheckoutDto::fromRequest($request->validated());

        try {
            $response = $this->checkout->execute($checkoutDto);

            return ApiResponse::success('Order created successfully', $response);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }
}
