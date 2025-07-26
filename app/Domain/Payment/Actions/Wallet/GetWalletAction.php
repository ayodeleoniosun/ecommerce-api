<?php

namespace App\Domain\Payment\Actions\Wallet;

use App\Application\Shared\Traits\UtilitiesTrait;
use App\Domain\Payment\Interfaces\Wallet\WalletRepositoryInterface;
use App\Infrastructure\Models\Payment\Wallet\Wallet;

class GetWalletAction extends BaseWalletAction
{
    use UtilitiesTrait;

    public function __construct(
        protected WalletRepositoryInterface $walletRepository,
    ) {
        parent::__construct($walletRepository);
    }

    public function execute(string $currency): Wallet
    {
        return $this->getWallet($currency);
    }
}
