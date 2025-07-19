<?php

namespace App\Domain\Order\Interfaces\Order;

use App\Infrastructure\Models\Cart\UserCart;
use App\Infrastructure\Models\Order\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface OrderRepositoryInterface
{
    public function index(string $currency): LengthAwarePaginator;

    public function findOrCreate(int $userId, UserCart $cart): Order;

    public function findPendingOrder(int $userId, bool $lockForUpdate = false): ?Order;

    public function storeOrUpdate(array $data): Order;
}
