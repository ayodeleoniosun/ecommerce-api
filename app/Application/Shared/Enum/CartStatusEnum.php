<?php

namespace App\Application\Shared\Enum;

enum CartStatusEnum: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case CHECKED_OUT = 'checked_out';
    case STOCK_RESTORED = 'stock_restored';
}
