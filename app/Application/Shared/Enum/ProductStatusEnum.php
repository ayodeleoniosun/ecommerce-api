<?php

namespace App\Application\Shared\Enum;

enum ProductStatusEnum: string
{
    case IN_STOCK = 'in stock';
    case OUT_OF_STOCK = 'out of stock';
}
