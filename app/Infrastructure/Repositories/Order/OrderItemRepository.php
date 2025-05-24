<?php

namespace App\Infrastructure\Repositories\Order;

use App\Application\Shared\Enum\OrderStatusEnum;
use App\Domain\Order\Interfaces\OrderItemRepositoryInterface;
use App\Infrastructure\Models\Order\OrderItem;
use App\Infrastructure\Repositories\BaseRepository;

class OrderItemRepository extends BaseRepository implements OrderItemRepositoryInterface
{
    public function storeOrUpdate(array $data): OrderItem
    {
        return OrderItem::updateOrCreate(
            [
                'order_id' => $data['order_id'],
                'product_item_id' => $data['product_item_id'],
                'status' => OrderStatusEnum::PENDING->value,
            ],
            $data,
        );
    }

    public function deleteItems(int $orderId): void
    {
        OrderItem::where('order_id', $orderId)->delete();
    }
}
