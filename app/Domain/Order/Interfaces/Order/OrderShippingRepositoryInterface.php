<?php

namespace App\Domain\Order\Interfaces\Order;

use App\Infrastructure\Models\Order\OrderShipping;

interface OrderShippingRepositoryInterface
{
    public function storeOrUpdate(array $data): OrderShipping;
}
