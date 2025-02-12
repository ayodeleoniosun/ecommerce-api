<?php

namespace App\Domains\Common\Enum;

enum  PaymentEnum: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case SUCCESS = 'success';
}
