<?php

namespace App\Domain\Order\Interfaces\Order;

use App\Infrastructure\Models\Order\OrderPayment;

interface OrderPaymentRepositoryInterface
{
    public function store(array $data): OrderPayment;
}
