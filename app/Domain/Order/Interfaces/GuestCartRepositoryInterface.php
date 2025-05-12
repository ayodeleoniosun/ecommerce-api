<?php

namespace App\Domain\Order\Interfaces;

use App\Domain\Order\Dtos\AddToCartDto;
use App\Infrastructure\Models\GuestCart;

interface GuestCartRepositoryInterface
{
    public function findOrCreate(AddToCartDto $addToCartDto): GuestCart;
}
