<?php

namespace App\Domain\Order\Interfaces\Cart;

use App\Domain\Order\Dtos\CartDto;
use App\Infrastructure\Models\Cart\UserCart;

interface UserCartRepositoryInterface
{
    public function findOrCreate(CartDto $addToCartDto): UserCart;

    public function findPendingCart(int $userId, bool $lockForUpdate = false): ?UserCart;

    public function storeOrUpdate(array $data): ?UserCart;
}
