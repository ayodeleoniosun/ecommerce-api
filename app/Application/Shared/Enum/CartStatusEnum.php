<?php

namespace App\Application\Shared\Enum;

enum CartStatusEnum: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case CHECKED_OUT = 'checked_out';
}
