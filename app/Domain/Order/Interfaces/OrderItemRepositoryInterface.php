<?php

namespace App\Domain\Order\Interfaces;

use App\Infrastructure\Models\Order\OrderItem;

interface OrderItemRepositoryInterface
{
    public function storeOrUpdate(array $data): OrderItem;

    public function deleteItems(int $orderId): void;
}
