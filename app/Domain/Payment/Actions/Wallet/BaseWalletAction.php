<?php

namespace App\Domain\Payment\Actions\Wallet;

use App\Domain\Payment\Interfaces\Wallet\WalletRepositoryInterface;
use App\Infrastructure\Models\Payment\Wallet\Wallet;

class BaseWalletAction
{
    public function __construct(
        private readonly WalletRepositoryInterface $walletRepository,
    ) {}

    protected function getWallet(string $currency): Wallet
    {
        $userId = auth()->user()->id;

        $wallet = $this->walletRepository->find($userId, $currency);

        if (! $wallet) {
            $wallet = $this->walletRepository->create([
                'user_id' => $userId,
                'currency' => $currency,
            ]);
        }

        $wallet->refresh();

        return $wallet;
    }
}
