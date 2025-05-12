<?php

namespace App\Application\Shared\Enum;

enum PaymentStatusEnum: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case SUCCESS = 'success';
}
