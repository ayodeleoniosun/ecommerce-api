<?php

namespace App\Domain\Payment\Interfaces\Wallet;

use App\Infrastructure\Models\Payment\Wallet\WalletOrderPayment;

interface WalletOrderPaymentRepositoryInterface
{
    public function create(array $data): WalletOrderPayment;
}
