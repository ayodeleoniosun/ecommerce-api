<?php

namespace App\Domain\Order\Interfaces;

use App\Infrastructure\Models\Order\OrderPayment;

interface OrderPaymentRepositoryInterface
{
    public function storeOrUpdate(array $data): OrderPayment;
}
