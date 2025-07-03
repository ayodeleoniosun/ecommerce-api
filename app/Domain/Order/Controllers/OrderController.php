<?php

namespace App\Domain\Order\Controllers;

use App\Application\Shared\Responses\ApiResponse;
use App\Domain\Order\Actions\Cart\Checkout;
use App\Domain\Order\Dtos\CheckoutDto;
use App\Domain\Order\Requests\CheckoutRequest;
use App\Domain\Payment\Constants\PaymentStatusEnum;
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

            if ($response->status === PaymentStatusEnum::FAILED->value) {
                $failureReason = $response->payments->last()->narration;

                return ApiResponse::error('Payment failed due to '.$failureReason, 400);
            }

            return ApiResponse::success('Order created successfully', $response);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }
}
