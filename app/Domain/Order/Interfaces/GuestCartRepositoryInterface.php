<?php

namespace App\Domain\Order\Interfaces;

use App\Domain\Order\Dtos\AddToCartDto;
use App\Infrastructure\Models\Cart\GuestCart;

interface GuestCartRepositoryInterface
{
    public function findOrCreate(AddToCartDto $addToCartDto): GuestCart;
}
