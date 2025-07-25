<?php

namespace App\Infrastructure\Repositories\Payment\Wallet;

use App\Domain\Payment\Interfaces\Wallet\WalletRepositoryInterface;
use App\Infrastructure\Models\Payment\Wallet\Wallet;
use App\Infrastructure\Repositories\BaseRepository;

class WalletRepository extends BaseRepository implements WalletRepositoryInterface
{
    public function create(array $data): Wallet
    {
        return Wallet::create($data);
    }

    public function find(int $userId, string $currency): ?Wallet
    {
        return Wallet::where('user_id', $userId)
            ->where('currency', $currency)
            ->first();
    }

    public function decrementBalance(Wallet $wallet, int $amount): void
    {
        $wallet->decrement('balance', $amount);
    }
}
