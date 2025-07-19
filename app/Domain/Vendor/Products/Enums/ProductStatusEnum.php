<?php

namespace App\Domain\Vendor\Products\Enums;

enum ProductStatusEnum: string
{
    case IN_STOCK = 'in_stock';
    case OUT_OF_STOCK = 'out_of_stock';
}
