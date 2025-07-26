<?php

namespace App\Domain\Payment\Controllers;

use App\Application\Shared\Responses\ApiResponse;
use App\Application\Shared\Traits\UtilitiesTrait;
use App\Domain\Payment\Actions\Wallet\GetWalletAction;
use Exception;
use Illuminate\Http\JsonResponse;

class WalletController
{
    use UtilitiesTrait;

    public function __construct(
        private readonly GetWalletAction $getWallet,
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
}
