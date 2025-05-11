<?php

namespace App\Application\Shared\Enum;

enum ProductEnum: string
{
    case IN_STOCK = 'in stock';
    case OUT_OF_STOCK = 'out of stock';
}
