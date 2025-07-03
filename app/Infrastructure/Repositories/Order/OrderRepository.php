<?php

namespace App\Infrastructure\Repositories\Order;

use App\Application\Shared\Enum\OrderStatusEnum;
use App\Domain\Order\Interfaces\OrderRepositoryInterface;
use App\Infrastructure\Models\Cart\UserCart;
use App\Infrastructure\Models\Order\Order;
use App\Infrastructure\Repositories\BaseRepository;

class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    public function storeOrUpdate(array $data): Order
    {
        return Order::updateOrCreate(
            ['id' => $data['id']],
            $data,
        );
    }

    public function findOrCreate(int $userId, UserCart $cart): Order
    {
        $order = Order::firstOrCreate(
            ['user_id' => $userId, 'cart_id' => $cart->id, 'status' => OrderStatusEnum::PENDING->value],
            ['user_id' => $userId, 'cart_id' => $cart->id, 'currency' => $cart->currency],
        );

        return $order->load('cart.items.productItem');
    }
}
