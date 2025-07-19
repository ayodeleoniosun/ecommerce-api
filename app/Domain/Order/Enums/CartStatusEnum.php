<?php

namespace App\Domain\Order\Enums;

enum CartStatusEnum: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case CHECKED_OUT = 'checked_out';
    case STOCK_RESTORED = 'stock_restored';
}
