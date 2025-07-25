<?php

namespace App\Domain\Payment\Interfaces\Wallet;

use App\Infrastructure\Models\Payment\Wallet\WalletTransaction;

interface WalletTransactionRepositoryInterface
{
    public function create(array $data): WalletTransaction;
}
