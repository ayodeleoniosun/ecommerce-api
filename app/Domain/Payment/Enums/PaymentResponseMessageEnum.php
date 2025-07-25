<?php

namespace App\Domain\Payment\Enums;

enum PaymentResponseMessageEnum: string
{
    case TRANSACTION_NOT_FOUND = 'Transaction not found';
    case TRANSACTION_ALREADY_COMPLETED = 'Transaction already completed';
    case TRANSACTION_SUCCESSFUL = 'Transaction successful';
}
