<?php

namespace App\Domain\Payment\Actions\Wallet;

use App\Application\Shared\Traits\UtilitiesTrait;
use App\Domain\Payment\Interfaces\Wallet\WalletRepositoryInterface;
use App\Domain\Payment\Interfaces\Wallet\WalletTransactionRepositoryInterface;
use App\Infrastructure\Models\Payment\Wallet\Wallet;

class GetWalletAction extends BaseWalletAction
{
    use UtilitiesTrait;

    public function __construct(
        protected WalletRepositoryInterface $walletRepository,
        protected WalletTransactionRepositoryInterface $walletTransactionRepository,
    ) {
        parent::__construct(
            $walletRepository,
            $walletTransactionRepository,
        );
    }

    public function execute(string $currency): Wallet
    {
        return $this->getWallet($currency);
    }
}
