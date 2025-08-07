<?php

namespace App\Domain\Payment\Interfaces\Wallet;

use App\Infrastructure\Models\Payment\Wallet\Wallet;

interface WalletRepositoryInterface
{
    public function create(array $data): Wallet;

    public function find(int $userId, string $currency): ?Wallet;

    public function decrementBalance(Wallet $wallet, int $amount): void;

    public function incrementBalance(Wallet $wallet, int $amount): void;
}
