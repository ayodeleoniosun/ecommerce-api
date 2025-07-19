<?php

namespace App\Domain\Order\Interfaces\Order;

use App\Infrastructure\Models\Order\OrderItem;

interface OrderItemRepositoryInterface
{
    public function storeOrUpdate(array $data): OrderItem;

    public function deleteOrderItems(int $orderId, array $productItems): void;
}
