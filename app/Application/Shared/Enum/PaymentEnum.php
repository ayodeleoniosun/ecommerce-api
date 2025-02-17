<?php

namespace App\Application\Shared\Enum;

enum  PaymentEnum: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case SUCCESS = 'success';
}
