<?php

namespace App\Domain\Order\Controllers;

use App\Application\Shared\Responses\ApiResponse;
use App\Application\Shared\Traits\UtilitiesTrait;
use App\Domain\Order\Actions\Order\CheckoutAction;
use App\Domain\Order\Actions\Order\GetOrderAction;
use App\Domain\Order\Actions\Order\GetOrdersAction;
use App\Domain\Order\Dtos\CheckoutDto;
use App\Domain\Order\Requests\CheckoutRequest;
use App\Domain\Payment\Actions\Order\AuthorizeOrderPaymentAction;
use App\Domain\Payment\Actions\Order\CompleteOrderPaymentAction;
use App\Domain\Payment\Actions\Order\InitiateOrderPaymentAction;
use App\Domain\Payment\Dtos\Card\PaymentDto;
use App\Domain\Payment\Dtos\PaymentAuthorizationDto;
use App\Domain\Payment\Enums\PaymentStatusEnum;
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
        private readonly AuthorizeOrderPaymentAction $authorizePayment,
        private readonly GetOrdersAction $getOrders,
        private readonly GetOrderAction $getOrder,
    ) {}

    public function index(string $currency): JsonResponse
    {
        try {
            $data = $this->getOrders->execute($currency);

            return ApiResponse::success('Orders retrieved', $data);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function view(string $UUID): JsonResponse
    {
        try {
            $data = $this->getOrder->execute($UUID);

            return ApiResponse::success('Order retrieved', $data);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

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
        $paymentDto = PaymentDto::fromArray($request->validated());

        try {
            $transactionResponse = $this->initiateOrderPayment->execute($paymentDto);

            $shouldUpdateTransactionStatus = self::shouldUpdateTransactionStatus($transactionResponse);

            if ($shouldUpdateTransactionStatus) {
                $this->completeOrderPayment->updateOrderPayment($transactionResponse);
            }

            if ($transactionResponse->getStatus() === PaymentStatusEnum::FAILED->value) {
                return ApiResponse::error($transactionResponse->getResponseMessage());
            }

            if (self::requiresAuthorization($transactionResponse->getAuthModel())) {
                return ApiResponse::success('Authorization required', $transactionResponse->toArray());
            }

            $response = $this->completeOrderPayment->execute($transactionResponse);

            return ApiResponse::success('Order successfully completed', $response);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function authorize(PaymentAuthorizationRequest $request): JsonResponse
    {
        $paymentAuthorizationDto = PaymentAuthorizationDto::fromArray($request->validated());

        try {
            $transactionResponse = $this->authorizePayment->execute($paymentAuthorizationDto);

            if ($transactionResponse->getStatus() === PaymentStatusEnum::FAILED->value) {
                $this->completeOrderPayment->updateOrderPayment($transactionResponse);

                return ApiResponse::error($transactionResponse->getResponseMessage());
            }

            $response = $this->completeOrderPayment->execute($transactionResponse);

            return ApiResponse::success('Order successfully completed', $response);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }
}
