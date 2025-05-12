<?php

namespace App\Infrastructure\Repositories\Order;

use App\Application\Shared\Enum\CartStatusEnum;
use App\Domain\Order\Dtos\AddToCartDto;
use App\Domain\Order\Interfaces\GuestCartRepositoryInterface;
use App\Infrastructure\Models\GuestCart;
use App\Infrastructure\Repositories\Inventory\BaseRepository;

class GuestCartRepository extends BaseRepository implements GuestCartRepositoryInterface
{
    public function findOrCreate(AddToCartDto $addToCartDto): GuestCart
    {
        return GuestCart::firstOrCreate(
            ['identifier' => $addToCartDto->getIdentifier(), 'status' => CartStatusEnum::PENDING->value],
            $addToCartDto->toCartArray(),
        );
    }
}
