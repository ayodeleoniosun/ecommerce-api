<?php

namespace App\Infrastructure\Repositories\Order;

use App\Application\Shared\Enum\OrderStatusEnum;
use App\Domain\Order\Interfaces\OrderRepositoryInterface;
use App\Infrastructure\Models\Order\Order;
use App\Infrastructure\Repositories\BaseRepository;

class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    public function findOrCreate(): Order
    {
        return Order::firstOrCreate(
            ['user_id' => auth()->user()->id, 'status' => OrderStatusEnum::PENDING->value],
            ['user_id' => auth()->user()->id],
        );
    }
}
