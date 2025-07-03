<?php

namespace App\Domain\Payment\Interfaces;

use App\Domain\Payment\Dtos\InitiateOrderPaymentDto;
use Illuminate\Database\Eloquent\Model;

interface CardTransactionRepositoryInterface
{
    public function create(int $orderPaymentId, InitiateOrderPaymentDto $paymentDto): Model;

    public function update(int $transactionId, array $data): ?Model;
}
