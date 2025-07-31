<?php

namespace App\Domain\Order\Enums;

enum CartStatusEnum: string
{
    case PENDING = 'pending';
    case CHECKED_OUT = 'checked_out';
    case OUT_OF_STOCK = 'out_of_stock';
    case STOCK_RESTORED = 'stock_restored';
}
