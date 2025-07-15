<?php

namespace App\Domain\Order\Controllers;

use App\Application\Shared\Responses\ApiResponse;
use App\Application\Shared\Traits\UtilitiesTrait;
use App\Domain\Order\Actions\CheckoutAction;
use App\Domain\Order\Dtos\CheckoutDto;
use App\Domain\Order\Requests\CheckoutRequest;
use App\Domain\Payment\Actions\AuthorizePaymentAction;
use App\Domain\Payment\Actions\CompleteOrderPaymentAction;
use App\Domain\Payment\Actions\InitiateOrderPaymentAction;
use App\Domain\Payment\Constants\PaymentStatusEnum;
use App\Domain\Payment\Dtos\OrderPaymentDto;
use App\Domain\Payment\Dtos\PaymentAuthorizationDto;
use App\Domain\Payment\Requests\OrderPaymentRequest;
use App\Domain\Payment\Requests\PaymentAuthorizationRequest;
use Exception;
use Illuminate\Http\JsonResponse;

class OrderController
{
    use UtilitiesTrait;

    public function __construct(
        private readonly CheckoutAction $checkout,
        private readonly InitiateOrderPaymentAction $initiateOrderPayment,
        private readonly CompleteOrderPaymentAction $completeOrderPayment,
        private readonly AuthorizePaymentAction $authorizePayment,
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

    public function pay(OrderPaymentRequest $request): JsonResponse
    {
        $orderPaymentDto = OrderPaymentDto::fromArray($request->validated());

        try {
            $transactionResponse = $this->initiateOrderPayment->execute($orderPaymentDto);

            if (self::requiresAuthorization($transactionResponse->getAuthModel())) {
                $this->completeOrderPayment->updateOrderPayment($transactionResponse);

                return ApiResponse::success('Authorization required', $transactionResponse->toArray());
            }

            $response = $this->completeOrderPayment->execute($transactionResponse);

            if ($response->status === PaymentStatusEnum::SUCCESS->value) {
                return ApiResponse::success('Order successfully completed', $response);
            }

            return ApiResponse::error($transactionResponse->getResponseMessage());
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function authorizePayment(PaymentAuthorizationRequest $request): JsonResponse
    {
        $paymentAuthorizationDto = PaymentAuthorizationDto::fromArray($request->validated());

        try {
            $transactionResponse = $this->authorizePayment->execute($paymentAuthorizationDto);

            if ($transactionResponse->getErrorType()) {
                return ApiResponse::error($transactionResponse->getResponseMessage());
            }

            $response = $this->completeOrderPayment->execute($transactionResponse);

            if ($transactionResponse->getStatus() === PaymentStatusEnum::SUCCESS->value) {
                return ApiResponse::success('Order successfully completed', $response);
            }

            return ApiResponse::error($transactionResponse->getResponseMessage());

        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }
}
