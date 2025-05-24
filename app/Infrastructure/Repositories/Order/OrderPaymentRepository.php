<?php

namespace App\Infrastructure\Repositories\Order;

use App\Domain\Order\Interfaces\OrderPaymentRepositoryInterface;
use App\Infrastructure\Models\Order\OrderPayment;
use App\Infrastructure\Repositories\BaseRepository;

class OrderPaymentRepository extends BaseRepository implements OrderPaymentRepositoryInterface
{
    public function storeOrUpdate(array $data): OrderPayment
    {
        return OrderPayment::updateOrCreate(
            ['order_id' => $data['order_id']],
            $data,
        );
    }
}
