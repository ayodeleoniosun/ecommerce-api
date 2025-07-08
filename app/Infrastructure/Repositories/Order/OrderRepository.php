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

    public function findPendingOrder(int $userId, bool $lockForUpdate = false): ?Order
    {
        return Order::with('cart.items.productItem')
            ->where('user_id', $userId)
            ->where('status', OrderStatusEnum::PENDING->value)
            ->when($lockForUpdate, fn ($query) => $query->lockForUpdate())
            ->first();
    }

    public function findOrCreate(int $userId, UserCart $cart): Order
    {
        return Order::firstOrCreate(
            ['user_id' => $userId, 'cart_id' => $cart->id, 'status' => OrderStatusEnum::PENDING->value],
            ['user_id' => $userId, 'cart_id' => $cart->id, 'currency' => $cart->currency],
        )->load('cart.items.productItem');
    }
}
