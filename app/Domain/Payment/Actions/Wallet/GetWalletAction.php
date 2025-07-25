<?php

namespace App\Domain\Payment\Actions\Wallet;

use App\Application\Shared\Traits\UtilitiesTrait;
use App\Domain\Order\Resources\Order\WalletResource;
use App\Domain\Payment\Interfaces\Wallet\WalletRepositoryInterface;

class GetWalletAction
{
    use UtilitiesTrait;

    public function __construct(
        private readonly WalletRepositoryInterface $walletRepository,
    ) {}

    public function execute(string $currency): WalletResource
    {
        $userId = auth()->user()->id;

        $wallet = $this->walletRepository->find($userId, $currency);

        if (! $wallet) {
            $wallet = $this->walletRepository->create([
                'user_id' => $userId,
                'currency' => $currency,
            ]);
        }

        return new WalletResource($wallet);
    }
}
