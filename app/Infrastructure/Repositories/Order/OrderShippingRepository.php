<?php

namespace App\Infrastructure\Repositories\Order;

use App\Domain\Order\Interfaces\OrderShippingRepositoryInterface;
use App\Infrastructure\Models\Order\OrderShipping;
use App\Infrastructure\Repositories\BaseRepository;

class OrderShippingRepository extends BaseRepository implements OrderShippingRepositoryInterface
{
    public function storeOrUpdate(array $data): OrderShipping
    {
        return OrderShipping::updateOrCreate(
            ['order_id' => $data['order_id']],
            $data,
        );
    }
}
