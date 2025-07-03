<?php

namespace App\Domain\Order\Interfaces;

use App\Infrastructure\Models\Cart\UserCart;
use App\Infrastructure\Models\Order\Order;

interface OrderRepositoryInterface
{
    public function findOrCreate(int $userId, UserCart $cart): Order;

    public function storeOrUpdate(array $data): Order;
}
