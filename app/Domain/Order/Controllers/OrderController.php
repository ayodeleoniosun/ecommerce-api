<?php

namespace App\Domain\Order\Controllers;

use App\Application\Shared\Responses\ApiResponse;
use App\Domain\Order\Actions\Checkout;
use App\Domain\Order\Actions\CompleteCartAction;
use App\Domain\Order\Dtos\CheckoutDto;
use App\Domain\Order\Requests\CheckoutRequest;
use App\Domain\Payment\Actions\CompleteOrderPaymentAction;
use App\Domain\Payment\Actions\InitiateOrderPaymentAction;
use App\Domain\Payment\Constants\PaymentStatusEnum;
use App\Domain\Payment\Dtos\CheckoutPaymentDto;
use App\Domain\Payment\Requests\CheckoutPaymentRequest;
use Exception;
use Illuminate\Http\JsonResponse;

class OrderController
{
    public function __construct(
        private readonly Checkout $checkout,
        private readonly InitiateOrderPaymentAction $initiateOrderPayment,
        private readonly CompleteOrderPaymentAction $completeOrderPayment,
        private readonly CompleteCartAction $completeCart,
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

    public function pay(CheckoutPaymentRequest $request): JsonResponse
    {
        $checkoutPaymentDto = CheckoutPaymentDto::fromArray($request->validated());

        try {
            $transactionResponse = $this->initiateOrderPayment->execute($checkoutPaymentDto);

            $response = $this->completeOrderPayment->execute($transactionResponse);

            if ($response->status === PaymentStatusEnum::SUCCESS->value) {
                $this->completeCart->execute($response->status);

                return ApiResponse::success('Payment successfully made', $response);
            }

            $failureReason = $response->payment->narration;

            return ApiResponse::error('Payment failed due to '.$failureReason, 400);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }
}
