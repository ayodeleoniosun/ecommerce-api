<?php

namespace App\Infrastructure\Repositories\Payment\Wallet;

use App\Domain\Payment\Interfaces\Wallet\WalletOrderPaymentRepositoryInterface;
use App\Infrastructure\Models\Payment\Wallet\WalletOrderPayment;
use App\Infrastructure\Repositories\BaseRepository;

class WalletOrderPaymentRepository extends BaseRepository implements WalletOrderPaymentRepositoryInterface
{
    public function create(array $data): WalletOrderPayment
    {
        return WalletOrderPayment::create($data);
    }
}
