<?php

namespace App\Domain\Order\Interfaces;

use App\Infrastructure\Models\Order\OrderShipping;

interface OrderShippingRepositoryInterface
{
    public function storeOrUpdate(array $data): OrderShipping;
}
