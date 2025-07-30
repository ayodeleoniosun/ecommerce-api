<?php

namespace App\Domain\Payment\Enums;

enum PaymentStatusEnum: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case SUCCESS = 'success';
    case SUCCESSFUL = 'successful';
    case FAILED = 'failed';
}
