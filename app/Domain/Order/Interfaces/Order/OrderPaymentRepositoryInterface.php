<?php

namespace App\Domain\Order\Interfaces\Order;

use App\Infrastructure\Models\Order\OrderPayment;

interface OrderPaymentRepositoryInterface
{
    public function storeOrUpdate(array $data): OrderPayment;
}
