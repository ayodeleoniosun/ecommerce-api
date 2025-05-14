<?php

namespace App\Domain\Order\Interfaces;

use App\Domain\Order\Dtos\AddToCartDto;
use App\Infrastructure\Models\Order\GuestCartItem;

interface GuestCartItemRepositoryInterface
{
    public function storeOrUpdate(AddToCartDto $addToCartDto): GuestCartItem;
}
