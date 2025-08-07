<?php

namespace App\Domain\Payment\Controllers;

use App\Application\Shared\Responses\ApiResponse;
use App\Application\Shared\Traits\UtilitiesTrait;
use App\Domain\Payment\Actions\Wallet\AuthorizeWalletFundingPaymentAction;
use App\Domain\Payment\Actions\Wallet\CompleteWalletFundingAction;
use App\Domain\Payment\Actions\Wallet\FundWalletAction;
use App\Domain\Payment\Actions\Wallet\GetWalletAction;
use App\Domain\Payment\Dtos\PaymentAuthorizationDto;
use App\Domain\Payment\Dtos\Wallet\FundWalletDto;
use App\Domain\Payment\Enums\PaymentStatusEnum;
use App\Domain\Payment\Requests\FundWalletRequest;
use App\Domain\Payment\Requests\PaymentAuthorizationRequest;
use Exception;
use Illuminate\Http\JsonResponse;

class WalletController
{
    use UtilitiesTrait;

    public function __construct(
        private readonly GetWalletAction $getWallet,
        private readonly FundWalletAction $fundWallet,
        private readonly CompleteWalletFundingAction $completeWalletFunding,
        private readonly AuthorizeWalletFundingPaymentAction $authorizeWalletFundingPayment,
    ) {}

    public function balance(string $currency): JsonResponse
    {
        try {
            $wallet = $this->getWallet->execute($currency);

            return ApiResponse::success('Wallet balance retrieved', $wallet->balance);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function fund(FundWalletRequest $request): JsonResponse
    {
        $fundWalletDto = FundWalletDto::fromArray($request->validated());

        try {
            $transactionResponse = $this->fundWallet->execute($fundWalletDto);

            $shouldUpdateTransactionStatus = self::shouldUpdateTransactionStatus($transactionResponse);

            if ($shouldUpdateTransactionStatus) {
                $this->completeWalletFunding->updateTransactionStatus($transactionResponse);
            }

            if ($transactionResponse->getStatus() === PaymentStatusEnum::FAILED->value) {
                return ApiResponse::error($transactionResponse->getResponseMessage());
            }

            if (self::requiresAuthorization($transactionResponse->getAuthModel())) {
                return ApiResponse::success('Authorization required', $transactionResponse->toArray());
            }

            $response = $this->completeWalletFunding->execute($transactionResponse);

            return ApiResponse::success('Wallet successfully funded', $response->toArray());
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function authorize(PaymentAuthorizationRequest $request): JsonResponse
    {
        $paymentAuthorizationDto = PaymentAuthorizationDto::fromArray($request->validated());

        try {
            $transactionResponse = $this->authorizeWalletFundingPayment->execute($paymentAuthorizationDto);

            if ($transactionResponse->getStatus() === PaymentStatusEnum::FAILED->value) {
                $this->completeWalletFunding->updateTransactionStatus($transactionResponse);

                return ApiResponse::error($transactionResponse->getResponseMessage());
            }

            $response = $this->completeWalletFunding->execute($transactionResponse);

            return ApiResponse::success('Wallet successfully funded', $response->toArray());
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }
}
