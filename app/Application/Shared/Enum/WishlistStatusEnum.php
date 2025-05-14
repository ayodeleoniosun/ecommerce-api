<?php

namespace App\Application\Shared\Enum;

enum WishlistStatusEnum: string
{
    case IN_STOCK = 'in_stock';
    case OUT_OF_STOCK = 'out_of_stock';
    case ADDED_TO_CART = 'added_to_cart';
}
