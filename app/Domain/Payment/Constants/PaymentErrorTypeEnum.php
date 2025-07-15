<?php

namespace App\Domain\Payment\Constants;

enum PaymentErrorTypeEnum: string
{
    case INVALID_REQUEST = 'INVALID_REQUEST';
    case TRANSACTION_NOT_FOUND = 'TRANSACTION_NOT_FOUND';
    case TRANSACTION_ALREADY_COMPLETED = 'TRANSACTION_ALREADY_COMPLETED';
}
