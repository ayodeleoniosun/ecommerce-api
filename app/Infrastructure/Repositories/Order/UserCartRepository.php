<?php

namespace App\Infrastructure\Repositories\Order;

use App\Application\Shared\Enum\CartStatusEnum;
use App\Domain\Order\Dtos\AddToCartDto;
use App\Domain\Order\Interfaces\UserCartRepositoryInterface;
use App\Infrastructure\Models\Cart\UserCart;
use App\Infrastructure\Repositories\BaseRepository;

class UserCartRepository extends BaseRepository implements UserCartRepositoryInterface
{
    public function findOrCreate(AddToCartDto $addToCartDto): UserCart
    {
        return UserCart::firstOrCreate(
            ['user_id' => $addToCartDto->getUserId(), 'status' => CartStatusEnum::PENDING->value],
            $addToCartDto->toCartArray(),
        );
    }

    public function findPendingCart(int $userId): ?UserCart
    {
        return UserCart::with('items', 'items.productItem')
            ->where('user_id', $userId)
            ->where('status', CartStatusEnum::PENDING->value)
            ->first();
    }
}
