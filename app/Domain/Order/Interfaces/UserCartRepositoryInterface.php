<?php

namespace App\Domain\Order\Interfaces;

use App\Domain\Order\Dtos\AddToCartDto;
use App\Infrastructure\Models\UserCart;

interface UserCartRepositoryInterface
{
    public function findOrCreate(AddToCartDto $addToCartDto): UserCart;

    public function findPendingCart(int $userId): ?UserCart;
}
