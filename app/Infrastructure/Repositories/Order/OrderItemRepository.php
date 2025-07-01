<?php

namespace App\Infrastructure\Repositories\Order;

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
                'cart_item_id' => $data['cart_item_id'],
                'total_amount' => $data['total_amount'],
            ],
            $data,
        );
    }

    public function deleteOrderItems(int $orderId, array $productItems): void
    {
        OrderItem::where('order_id', $orderId)
            ->whereIn('cart_item_id', $productItems)
            ->delete();
    }
}
