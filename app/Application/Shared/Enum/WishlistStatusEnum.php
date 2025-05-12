<?php

namespace App\Application\Shared\Enum;

enum WishlistStatusEnum: string
{
    case IN_STOCK = 'in stock';
    case OUT_OF_STOCK = 'out of stock';
    case ADDED_TO_CART = 'added_to_cart';
}
