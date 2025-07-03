<?php

namespace App\Domain\Payment\Constants;

enum PaymentStatusEnum: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case SUCCESS = 'success';
    case FAILED = 'failed';
}
