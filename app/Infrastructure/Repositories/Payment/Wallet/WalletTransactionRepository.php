<?php

namespace App\Infrastructure\Repositories\Payment\Wallet;

use App\Domain\Payment\Interfaces\Wallet\WalletTransactionRepositoryInterface;
use App\Infrastructure\Models\Payment\Wallet\WalletTransaction;
use App\Infrastructure\Repositories\BaseRepository;

class WalletTransactionRepository extends BaseRepository implements WalletTransactionRepositoryInterface
{
    public function create(array $data): WalletTransaction
    {
        return WalletTransaction::create($data);
    }
}
