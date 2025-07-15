<?php

namespace App\Domain\Payment\Constants;

enum PaymentErrorEnum: string
{
    case TRANSACTION_NOT_FOUND = 'Transaction not found';
    case TRANSACTION_ALREADY_COMPLETED = 'Transaction already completed';
}
