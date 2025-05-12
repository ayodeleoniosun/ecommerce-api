<?php

namespace App\Domain\Order\Interfaces;

use App\Domain\Order\Dtos\AddToCartDto;
use App\Infrastructure\Models\UserCartItem;

interface UserCartItemRepositoryInterface
{
    public function storeOrUpdate(AddToCartDto $addToCartDto): UserCartItem;

    public function findExistingCartItem(int $cartId, int $productItemId): ?UserCartItem;
}
