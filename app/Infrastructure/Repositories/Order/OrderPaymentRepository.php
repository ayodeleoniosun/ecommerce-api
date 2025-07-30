<?php

namespace App\Infrastructure\Repositories\Order;

use App\Domain\Order\Interfaces\Order\OrderPaymentRepositoryInterface;
use App\Infrastructure\Models\Order\OrderPayment;
use App\Infrastructure\Repositories\BaseRepository;

class OrderPaymentRepository extends BaseRepository implements OrderPaymentRepositoryInterface
{
    public function store(array $data): OrderPayment
    {
        return OrderPayment::create($data);
    }
}
