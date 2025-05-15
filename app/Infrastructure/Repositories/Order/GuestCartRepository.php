<?php

namespace App\Infrastructure\Repositories\Order;

use App\Application\Shared\Enum\CartStatusEnum;
use App\Domain\Order\Dtos\AddToCartDto;
use App\Domain\Order\Interfaces\GuestCartRepositoryInterface;
use App\Infrastructure\Models\Cart\GuestCart;
use App\Infrastructure\Repositories\BaseRepository;

class GuestCartRepository extends BaseRepository implements GuestCartRepositoryInterface
{
    public function findOrCreate(AddToCartDto $addToCartDto): GuestCart
    {
        return \App\Infrastructure\Models\Cart\GuestCart::firstOrCreate(
            ['identifier' => $addToCartDto->getIdentifier(), 'status' => CartStatusEnum::PENDING->value],
            $addToCartDto->toCartArray(),
        );
    }
}
